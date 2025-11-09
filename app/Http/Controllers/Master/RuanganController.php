<?php

namespace App\Http\Controllers\Master;

use DataTables;
use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use App\Http\Requests\StoreRuanganRequest;
use App\Http\Requests\UpdateRuanganRequest;
use App\Models\Departemen;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Satusehat\Integration\FHIR\Location;

class RuanganController extends Controller
{
    public function dt()
    {

        $ruangan = Ruangan::query()
            ->with('departemen');

        return DataTables::of($ruangan)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return "
                                <button class='btn btn-warning btn-icon' onclick='handleModal(`edit`, `Ubah Ruangan`, " . json_encode($row) . ")'>
                                    <i class='ti ti-edit'></i>
                                </button>
                                <button class='btn btn-danger btn-icon' onclick='confirmDelete(`" . route('api.master.ruangan.destroy', $row->id) . "`, table.ajax.reload)'>
                                    <i class='ti ti-trash'></i>
                                </button>
                            ";
            })
            ->rawColumns([
                'action',
            ])
            ->make(true);
    }

    public function index()
    {
        $departemen = Departemen::all();

        return view('master.ruangan.index', compact('departemen'));
    }

    public function store(StoreRuanganRequest $request)
    {

        DB::beginTransaction();

        try {
            $data = $request->only([
                'name',
                'departemen_id'
            ]);

            $data['id'] = Str::uuid();

            $departemen = Departemen::find($request->departemen_id);

            if ($departemen) {
                $location = new Location;
                $location->addIdentifier($data['id']);
                $location->setName($request->name);
                $location->setManagingOrganization($departemen->ihs_id);

                [
                    $statusCode,
                    $response
                ] = $location->post();

                if ($statusCode == 201) {
                    $ihsId = $response->id;
                    $data['ihs_id'] = $ihsId;
                }
            }



            Ruangan::create($data);


            DB::commit();

            return $this->sendResponse(message: __('http-response.success.store', ['Attribute' => 'Ruangan']));
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->sendError(message: __('http-response.error.store', ['Attribute' => 'Ruangan']), errors: $th->getMessage(), traces: $th->getTrace());
        }
    }

    public function update(UpdateRuanganRequest $request, Ruangan $ruangan)
    {
        DB::beginTransaction();

        try {
            $data = $request->only([
                'name',
                'departemen_id'
            ]);

            $departemen = Departemen::find($request->departemen_id);

            if ($departemen) {
                $location = new Location;
                $location->addIdentifier($ruangan->ihs_id);
                $location->setName($request->name);
                $location->setManagingOrganization($departemen->ihs_id);

                $location->put($ruangan->ihs_id);
            }

            Ruangan::where('id', $ruangan->id)->update($data);

            DB::commit();

            return $this->sendResponse(message: __('http-response.success.update', ['Attribute' => 'Ruangan']));
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->sendError(message: __('http-response.success.update', ['Attribute' => 'Ruangan']), errors: $th->getMessage(), traces: $th->getTrace());
        }
    }

    public function destroy(Ruangan $ruangan)
    {
        $ruangan->delete();

        return $this->sendResponse(message: __('http-response.success.delete', ['Attribute' => 'Ruangan']));
    }
}
