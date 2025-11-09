<?php

namespace App\Http\Controllers\Master;

use DB;
use DataTables;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

use Satusehat\Integration\OAuth2Client;

class UserController extends Controller
{
    public function dt()
    {

        $user = User::query();

        return DataTables::of($user)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {

                if ($row->username != 'admin') {
                    return "
                                <button class='btn btn-warning btn-icon' onclick='handleModal(`edit`, `Ubah Pengguna`, " . json_encode($row) . ")'>
                                    <i class='ti ti-edit'></i>
                                </button>
                                <button class='btn btn-danger btn-icon' onclick='confirmDelete(`" . route('api.master.pengguna.destroy', $row->id) . "`, table.ajax.reload)'>
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
        $roles = roles();

        return view('master.pengguna.index', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {

        DB::beginTransaction();

        try {
            $data = $request->only([
                'name',
                'username',
                'password',
                'role',
            ]);

            if ($request->nakes) {
                $client = new OAuth2Client();

                [$statusCode, $response] = $client->get_by_nik('Practitioner', $request->nik);

                if ($statusCode == 200) {
                    $data['ihs_id'] = $response->entry[0]?->resource?->id;
                }

                $data['nik'] = $request->nik;
            }

            $data['password'] = bcrypt($request->password);

            User::create($data);

            DB::commit();

            return $this->sendResponse(message: __('http-response.success.store', ['Attribute' => 'Pengguna']));
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->sendError(message: __('http-response.error.store', ['Attribute' => 'Pengguna']), errors: $th->getMessage(), traces: $th->getTrace());
        }
    }

    public function update(UpdateUserRequest $request, User $pengguna)
    {
        DB::beginTransaction();

        try {
            $data = $request->only([
                'name',
                'username',
                'password',
                'role',
            ]);

            if ($request->nakes) {
                $client = new OAuth2Client();

                [$statusCode, $response] = $client->get_by_nik('Practitioner', $request->nik);

                if ($statusCode == 200) {
                    $data['ihs_id'] = $response->entry[0]?->resource?->id;
                }
                $data['nik'] = $request->nik;
            }

            if (!$request->nakes) {
                $data['ihs_id'] = null;
            }

            if (!empty($request->password)) {
                $data['password'] = bcrypt($request->password);
            }


            User::where('id', $pengguna->id)->update($data);

            DB::commit();

            return $this->sendResponse(message: __('http-response.success.update', ['Attribute' => 'Pengguna']));
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->sendError(message: __('http-response.success.update', ['Attribute' => 'Pengguna']), errors: $th->getMessage(), traces: $th->getTrace());
        }
    }

    public function destroy(User $pengguna)
    {
        $pengguna->delete();

        return $this->sendResponse(message: __('http-response.success.delete', ['Attribute' => 'Pengguna']));
    }
}
