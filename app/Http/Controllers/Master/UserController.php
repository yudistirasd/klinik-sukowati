<?php

namespace App\Http\Controllers\Master;

use DB;
use DataTables;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Ruangan;
use Illuminate\Http\Request;
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
                            <a class='btn btn-dark btn-icon' href='" . route('master.pengguna.setting', $row->id) . "'>
                                <i class='ti ti-settings'></i>
                            </a>
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

    public function storeDokterExternal(Request $request)
    {
        return response()->json([
            "code" => 200,
            "message" => "Dokter External berhasil disimpan.",
            "data" => [
                "dokter_external" => [
                    "name" => "xxx",
                    "username" => "dokter_external_692148cf71e8b",
                    "role" => "dokter",
                    "id" => "019aaa04-6b45-71dd-a088-0bfef3a0608d",
                    "updated_at" => "2025-11-22T05:23:27.000000Z",
                    "created_at" => "2025-11-22T05:23:27.000000Z",
                    "name_plain" => "xxx",
                ],
                "options_dokter" => [
                    [
                        "id" => "019a2bf3-b112-7346-8be2-dead8037ebc3",
                        "name" => "dr. Alexander",
                        "avatar" => "019a2bf3-b112-7346-8be2-dead8037ebc3.png",
                        "username" => "dokter",
                        "role" => "dokter",
                        "nik" => "7209061211900001",
                        "ihs_id" => "10009880728",
                        "created_at" => "2025-10-28T17:53:02.000000Z",
                        "updated_at" => "2025-11-11T23:06:13.000000Z",
                        "deleted_at" => null,
                        "name_plain" => "Alexander",
                    ],
                    [
                        "id" => "019a7d9f-1f4b-72b8-bd4b-af7ca39f7e00",
                        "name" => "Dokter B",
                        "avatar" => null,
                        "username" => "dokterb",
                        "role" => "dokter",
                        "nik" => "-",
                        "ihs_id" => null,
                        "created_at" => "2025-11-13T14:29:31.000000Z",
                        "updated_at" => "2025-11-13T14:29:31.000000Z",
                        "deleted_at" => null,
                        "name_plain" => "Dokter B",
                    ],
                    [
                        "id" => "019aaa04-6b45-71dd-a088-0bfef3a0608d",
                        "name" => "IHIRR",
                        "avatar" => null,
                        "username" => "dokter_external_692148cf71e8b",
                        "role" => "dokter",
                        "nik" => null,
                        "ihs_id" => null,
                        "created_at" => "2025-11-22T05:23:27.000000Z",
                        "updated_at" => "2025-11-22T05:23:27.000000Z",
                        "deleted_at" => null,
                        "name_plain" => "xxx",
                    ],
                ],
            ],
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => 'dokter_external_' . uniqid(),
            'password' => bcrypt(str()->random(16)),
            'role' => 'dokter',
            'dokter_external' => 'Y',
        ]);

        $dokter = User::dokter('Y')->get();

        return response()->json([
            "code" => 200,
            "message" => "Dokter External berhasil disimpan.",
            "data" => [
                "dokter_external" => [
                    "name" => "xxx",
                    "username" => "dokter_external_692148cf71e8b",
                    "role" => "dokter",
                    "id" => "019aaa04-6b45-71dd-a088-0bfef3a0608d",
                    "updated_at" => "2025-11-22T05:23:27.000000Z",
                    "created_at" => "2025-11-22T05:23:27.000000Z",
                    "name_plain" => "xxx",
                ],
                "options_dokter" => [
                    [
                        "id" => "019a2bf3-b112-7346-8be2-dead8037ebc3",
                        "name" => "dr. Alexander",
                        "avatar" => "019a2bf3-b112-7346-8be2-dead8037ebc3.png",
                        "username" => "dokter",
                        "role" => "dokter",
                        "nik" => "7209061211900001",
                        "ihs_id" => "10009880728",
                        "created_at" => "2025-10-28T17:53:02.000000Z",
                        "updated_at" => "2025-11-11T23:06:13.000000Z",
                        "deleted_at" => null,
                        "name_plain" => "Alexander",
                    ],
                    [
                        "id" => "019a7d9f-1f4b-72b8-bd4b-af7ca39f7e00",
                        "name" => "Dokter B",
                        "avatar" => null,
                        "username" => "dokterb",
                        "role" => "dokter",
                        "nik" => "-",
                        "ihs_id" => null,
                        "created_at" => "2025-11-13T14:29:31.000000Z",
                        "updated_at" => "2025-11-13T14:29:31.000000Z",
                        "deleted_at" => null,
                        "name_plain" => "Dokter B",
                    ],
                    [
                        "id" => "019aaa04-6b45-71dd-a088-0bfef3a0608d",
                        "name" => "xxx",
                        "avatar" => null,
                        "username" => "dokter_external_692148cf71e8b",
                        "role" => "dokter",
                        "nik" => null,
                        "ihs_id" => null,
                        "created_at" => "2025-11-22T05:23:27.000000Z",
                        "updated_at" => "2025-11-22T05:23:27.000000Z",
                        "deleted_at" => null,
                        "name_plain" => "xxx",
                    ],
                ],
            ],
        ]);

        return $this->sendResponse(message: __('http-response.success.store', ['Attribute' => 'Dokter External']), data: [
            'dokter_external' => $user,
            'options_dokter' => $dokter,
        ]);
    }

    public function setting(User $pengguna)
    {
        $ruangan = Ruangan::all();

        return view('master.pengguna.setting', compact('pengguna', 'ruangan'));
    }

    public function storeSettingRuangan(User $pengguna, Request $request)
    {
        $request->validate([
            'ruangan_id' => 'required',
            'user_id' => 'required'
        ]);

        $pengguna->ruangan()->syncWithoutDetaching($request->ruangan_id);

        return $this->sendResponse(message: __('http-response.success.update', ['Attribute' => 'Pengaturan Ruangan Pengguna']));
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
