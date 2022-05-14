<?php

namespace Phobrv\BrvProduct\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Phobrv\BrvCore\Repositories\PostRepository;
use Phobrv\BrvCore\Repositories\TermRepository;
use Phobrv\BrvCore\Repositories\UserRepository;
use Phobrv\BrvCore\Services\ConfigLangService;
use Phobrv\BrvCore\Services\UnitServices;
use Phobrv\BrvCore\Services\VString;
use Yajra\Datatables\Datatables;
use Phobrv\BrvCore\Services\PostServices;


class ProductController extends Controller
{
    protected $configLangService;
    protected $userRepository;
    protected $termRepository;
    protected $postRepository;
    protected $postService;
    protected $unitService;
    protected $taxonomy;
    protected $type;
    protected $vstring;
    protected $langMain;

    public function __construct(
        VString $vstring,
        ConfigLangService $configLangService,
        UserRepository $userRepository,
        TermRepository $termRepository,
        PostRepository $postRepository,
        PostServices $postService,
        UnitServices $unitService
    ) {
        $this->vstring = $vstring;
        $this->configLangService = $configLangService;
        $this->userRepository = $userRepository;
        $this->termRepository = $termRepository;
        $this->postRepository = $postRepository;
        $this->postService = $postService;
        $this->unitService = $unitService;
        $this->taxonomy = config('term.taxonomy.productgroup');
        $this->type = config('option.post_type.product');
        $this->langMain = $configLangService->getMainLang();
    }

    public function index()
    {
        $data['breadcrumbs'] = $this->unitService->generateBreadcrumbs(
            [
                ['text' => 'Products', 'href' => ''],
            ]
        );
        try {
            $user = Auth::user();
            $data['select'] = $this->userRepository->getMetaValueByKey($user, 'product_select');
            $data['arrayGroup'] = $this->termRepository->getArrayTerms($this->taxonomy);
            return view('phobrv::product.index')->with('data', $data);
        } catch (Exception $e) {
            return back()->with('alert_danger', $e->getMessage());
        }
    }
    public function getData()
    {
        $user = Auth::user();
        $data['select'] = $this->userRepository->getMetaValueByKey($user, 'product_select');
        if (!isset($data['select']) || $data['select'] == 0) {
            $data['products'] = $this->postRepository->all()->where('type', 'product')->where('lang', $this->langMain)->sortByDesc('created_at');
        } else {
            $data['products'] = $this->termRepository->getPostsByTermID($data['select'])->where('lang', $this->langMain);
        }

        $langArray = $this->configLangService->getArrayLangConfig();

        $i = 0;
        foreach ($data['products'] as $key => $value) {
            $i++;
            $data['products'][$key]['i'] = $i;
            $data['products'][$key]->buttons = $this->configLangService->genLangButton($value->id, $langArray);
        }

        return Datatables::of($data['products'])
        ->addColumn('title', function ($product) {
            return view('phobrv::product.components.viewTitle', ['product' => $product]);
        })
        ->addColumn('edit', function ($product) {
            return view('phobrv::product.components.editBtn', ['product' => $product]);
        })
        ->addColumn('status', function ($product) {
            return view('phobrv::product.components.statusLabel', ['product' => $product]);
        })
        ->addColumn('langButtons', function ($product) {
            return view('phobrv::product.components.langButtons', ['buttons' => $product->buttons]);
        })
        ->addColumn('delete', function ($product) {
            return view('phobrv::product.components.deleteBtn', ['product' => $product]);
        })
        ->make(true);
    }

    public function create()
    {
        //Breadcrumb
        $data['breadcrumbs'] = $this->unitService->generateBreadcrumbs(
            [
                ['text' => 'Products', 'href' => ''],
                ['text' => 'Create', 'href' => ''],
            ]
        );

        try {
            $data['group'] = $this->termRepository->getTermsOrderByParent($this->taxonomy);
            $data['arrayGroupID'] = array();
            $data['lang'] = $this->configLangService->getMainLang();
            return view('phobrv::product.create')->with('data', $data);
        } catch (Exception $e) {
            return back()->with('alert_danger', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->merge(['slug' => $this->vstring->standardKeyword($request->title)]);
        $request->validate(
            [
                'slug' => 'required|unique:posts',
            ],
            [
                'slug.unique' => 'Title đã tồn tại',
                'slug.required' => 'Title không được phép để rỗng',
            ]
        );

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['type'] = $this->type;
        $post = $this->postRepository->create($data);
        $this->configLangService->createTermLang($post);
        $msg = __('Create prodcut success!');

        if ($request->typeSubmit == 'save') {
            return redirect()->route('product.index')->with('alert_success', $msg);
        } else {
            return redirect()->route('product.edit', ['product' => $post->id])->with('alert_success', $msg);
        }

    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //Breadcrumb
        $data['breadcrumbs'] = $this->unitService->generateBreadcrumbs(
            [
                ['text' => 'Products', 'href' => ''],
                ['text' => 'Edit', 'href' => ''],
            ]
        );

        try {
            $data['group'] = $this->termRepository->getTermsOrderByParent($this->taxonomy);
            $data['post'] = $this->postRepository->find($id);
            $data['boxTranslate'] = $this->configLangService->genLangTranslateBox($data['post']);
            $data['arrayGroupID'] = $this->termRepository->getArrayTermIDByTaxonomy($data['post']->terms, $this->taxonomy);
            $data['gallery'] = $this->postService->getMultiMetaByKey($data['post']->postMetas, "image");
            $data['meta'] = $this->postService->getMeta($data['post']->postMetas);
            return view('phobrv::product.edit')->with('data', $data);
        } catch (Exception $e) {
            return back()->with('alert_danger', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->merge(['slug' => $this->vstring->standardKeyword($request->title)]);
        $request->validate([
            'slug' => 'required|unique:posts,slug,' . $id,
        ]);
        $data = $request->all();

        $post = $this->postRepository->update($data, $id);

        if (isset($data['group'])) {
            $this->syncGroupLang($post, $data['group']);
        }
        $this->postRepository->handleSeoMeta($post, $request);
        $msg = __('Update  prodcut success!');
        if ($request->typeSubmit == 'save') {
            return redirect()->route('product.index')->with('alert_success', $msg);
        } else {
            return redirect()->route('product.edit', ['product' => $post->id])->with('alert_success', $msg);
        }

    }

    public function destroy($id)
    {
        $this->postRepository->destroy($id);
        $msg = __("Delete post success!");
        return redirect()->route('product.index')->with('alert_success', $msg);
    }

    public function updateUserSelectGroup(Request $request)
    {
        $user = Auth::user();
        $this->userRepository->insertMeta($user, array('product_select' => $request->select));
        return redirect()->route('product.index');
    }

    public function uploadGallery(Request $request)
    {
        $data = $request->all()['data'];
        $product = $this->postRepository->find($data['product_id']);

        $images = explode(",", $data['images']);
        if ($images && count($images) > 0) {
            foreach ($images as $key => $path) {
                $this->postRepository->insertMeta($product, ['image' => $path], 'multi');
            }
        }
        return response()->json([
            'msg' => 'success',
            'message' => 'Update gallery success!',
        ]);
    }

    public function apiDelete(Request $request)
    {
        $data = $request->all();
        $this->postRepository->destroy($data['id']);
        return response()->json(['code' => '0', 'msg' => 'success']);
    }

    public function deleteMetaAPI(Request $request)
    {
        $meta_id = $request->meta_id;
        $this->postRepository->removeMeta($meta_id);
        return $meta_id;
    }

    public function syncGroupLang($post, $arrGroup)
    {
        $term = $post->terms->where('taxonomy', config('term.taxonomy.lang'))->first();
        $productgroup = $this->termRepository->getArrayTermIDByTaxonomy($post->terms, 'productgroup');

        if ($term) {
            $posts = $this->termRepository->find($term->id)->posts;
            foreach ($posts as $post) {
                $post->terms()->detach($productgroup);
                $post->terms()->attach($arrGroup);
            }
        } else {
            $post->terms()->sync($arrGroup);
        }
    }
}
