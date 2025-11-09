<?php

namespace App\Http\Controllers\Master;

use DB;
use DataTables;
use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Http\Requests\StoreDepartemenRequest;
use App\Http\Requests\UpdateDepartemenRequest;
use Satusehat\Integration\FHIR\Organization;
use Str;

class DepartemenController extends Controller
{
    public function dt()
    {

        $departemen = Departemen::query();

        return DataTables::of($departemen)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {

                if ($row->username != 'admin') {
                    return "
                                <button class='btn btn-warning btn-icon' onclick='handleModal(`edit`, `Ubah Departemen`, " . json_encode($row) . ")'>
                                    <i class='ti ti-edit'></i>
                                </button>
                                <button class='btn btn-danger btn-icon' onclick='confirmDelete(`" . route('api.master.departemen.destroy', $row->id) . "`, table.ajax.reload)'>
                                    <i class='ti ti-trash'></i>
                                </button>
                            ";
                }
            })
            ->rawColumns([
                'action',
            ])
            ->make(true);
    }

    public function index()
    {
        return view('master.departemen.index');
    }

    public function store(StoreDepartemenRequest $request)
    {

        DB::beginTransaction();

        try {
            $data = $request->only([
                'name',
            ]);

            $data['id'] = Str::uuid();

            $organization = new Organization;
            $organization->addIdentifier($data['id']);
            $organization->setName($request->name);
            $organization->setType('prov');

            [
                $statusCode,
                $response
            ] = $organization->post();

            if ($statusCode == 201) {
                $ihsId = $response->id;
                $data['ihs_id'] = $ihsId;
            }


            Departemen::create($data);


            DB::commit();

            return $this->sendResponse(message: __('http-response.success.store', ['Attribute' => 'Departemen']));
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->sendError(message: __('http-response.error.store', ['Attribute' => 'Departemen']), errors: $th->getMessage(), traces: $th->getTrace());
        }
    }

    public function update(UpdateDepartemenRequest $request, Departemen $departemen)
    {
        DB::beginTransaction();

        try {
            $data = $request->only([
                'name',
            ]);

            $organization = new Organization;
            $organization->addIdentifier($departemen->id);
            $organization->setName($request->name);
            $organization->setType('prov');
            $organization->put($departemen->ihs_id);

            Departemen::where('id', $departemen->id)->update($data);

            DB::commit();

            return $this->sendResponse(message: __('http-response.success.update', ['Attribute' => 'Departemen']));
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->sendError(message: __('http-response.success.update', ['Attribute' => 'Departemen']), errors: $th->getMessage(), traces: $th->getTrace());
        }
    }

    public function destroy(Departemen $departemen)
    {
        $departemen->delete();

        return $this->sendResponse(message: __('http-response.success.delete', ['Attribute' => 'Departemen']));
    }
}
