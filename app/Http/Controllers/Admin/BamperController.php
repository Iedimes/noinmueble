<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Bamper\BulkDestroyBamper;
use App\Http\Requests\Admin\Bamper\DestroyBamper;
use App\Http\Requests\Admin\Bamper\IndexBamper;
use App\Http\Requests\Admin\Bamper\StoreBamper;
use App\Http\Requests\Admin\Bamper\UpdateBamper;
use App\Models\Cartera;
use Brackets\AdminListing\Facades\AdminListing;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BamperController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexBamper $request
     * @return array|Factory|View
     */
    public function index(IndexBamper $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Cartera::class)
        ->attachSearch($request->queryString,
            // specify an array of columns to search in
            ['PerCod', 'PylCod', 'CliEsv', 'ManCod', 'VivLote', 'VivBlo', 'CliCMor']
        )
        ->get(['PerCod', 'PylCod', 'CliEsv', 'ManCod', 'VivLote', 'VivBlo', 'CliCMor']);
                //  ->attachSearch($request->queryString,
                //      // specify an array of columns to search in
                //      ['PerCod', 'PylCod', 'ManCod', 'VivLote', 'VivBlo', 'CliEsv']
                //  //->attachOrdering('id')
                // //  ->attachPagination($request->currentPage)
                // //  ->modifyQuery(function ($query) use ($request) {

                //     //  $query->where('CliEsv', 1);
                //     //  where('CliCMor', '<=', 3);
                //     // //  $query->where('TexCod', 188);
                //     //  if ($request->search) {

                //     //      $query->where(function ($query) use ($request) {
                //     //      $query->where('PerCod', $request->search);
                //     //     //    ->orWhere('NroExp', $request->search);
                //     //        })->where(function ($query) {
                //     //           $query->where('CliEsv', 1);
                //     //           $query->where('CliCMor', '<=', 3);
                //     //       });
                //     //      //return 'funciona';

                //      //$query->Where('NroExpsol', 'like', '%' . $request->search . '%');
                //          //$query->Where('NroExpPer', $request->search)
                //          //->OrWhere('NroExp', $request->search);
                //          //
                //          //$query->OrWhere('NroExp', $request->search);

                //      //}
                //      //return 'No Funciona';
                //  )
                //  //->paginate(15)
                //  ->get(['PerCod', 'PylCod', 'ManCod', 'VivLote', 'VivBlo', 'CliEsv']);



             //  return $request;

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('PerCod')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.bamper.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.bamper.create');

        return view('admin.bamper.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreBamper $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreBamper $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Bamper
        $bamper = Bamper::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/bampers'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/bampers');
    }

    /**
     * Display the specified resource.
     *
     * @param Bamper $bamper
     * @throws AuthorizationException
     * @return void
     */
    public function show(Bamper $bamper)
    {
        $this->authorize('admin.bamper.show', $bamper);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Bamper $bamper
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Bamper $bamper)
    {
        $this->authorize('admin.bamper.edit', $bamper);


        return view('admin.bamper.edit', [
            'bamper' => $bamper,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateBamper $request
     * @param Bamper $bamper
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateBamper $request, Bamper $bamper)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Bamper
        $bamper->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/bampers'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/bampers');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyBamper $request
     * @param Bamper $bamper
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyBamper $request, Bamper $bamper)
    {
        $bamper->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyBamper $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyBamper $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Bamper::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
