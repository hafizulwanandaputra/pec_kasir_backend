<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Outpatient;
use App\Http\Response\GlobalResponse;

class OutpatientController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $perPage = $request->input('per_page', Outpatient::count());
            $page = $request->input('page', 1);

            $skip = ($page - 1) * $perPage;

            $query = Outpatient::query();

            if (!empty($search)) {
                $query->where('no_registration', 'like', "%{$search}%");
                $query->orWhere('patient_name', 'like', "%{$search}%");
            }

            //get all posts
            // $data = Service::with('detail_transaction')->latest()->paginate(5);
            $data = $query->orderBy('id', 'desc')
                ->skip($skip)
                ->take($perPage)
                ->get();

            //return collection of data as a resource
            // return new Resources(true, 'List Layanan', $posts);
            return GlobalResponse::jsonResponse(
                $data,
                200,
                'success',
                'Rawat jalan berhasil diambil'
            );
        } catch (\Throwable $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        } catch (\Exception $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $data = Outpatient::find($id);
            return GlobalResponse::jsonResponse(
                $data,
                200,
                'success',
                'Rawat jalan berhasil diambil'
            );
        } catch (\Throwable $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        } catch (\Exception $e) {
            return GlobalResponse::jsonResponse(null, 500, 'error', $e->getMessage());
        }
    }
}
