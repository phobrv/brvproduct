<?php

namespace Phobrv\BrvProduct\Controllers;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Phobrv\BrvCore\Repositories\PostRepository;
use Phobrv\BrvCore\Repositories\TermRepository;
use Phobrv\BrvCore\Repositories\UserRepository;
use Phobrv\BrvCore\Services\UnitServices;
use Phobrv\BrvCore\Services\VString;
use Yajra\Datatables\Datatables;

class ProductController extends Controller {
	protected $userRepository;
	protected $termRepository;
	protected $postRepository;
	protected $unitService;
	protected $taxonomy;
	protected $type;
	protected $vstring;

	public function __construct(
		VString $vstring,
		UserRepository $userRepository,
		TermRepository $termRepository,
		PostRepository $postRepository,
		UnitServices $unitService
	) {
		$this->vstring = $vstring;
		$this->userRepository = $userRepository;
		$this->termRepository = $termRepository;
		$this->postRepository = $postRepository;
		$this->unitService = $unitService;
		$this->taxonomy = config('term.taxonomy.productgroup');
		$this->type = config('option.post_type.product');
	}

	public function index() {
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
	public function getData() {
		$user = Auth::user();
		$data['select'] = $this->userRepository->getMetaValueByKey($user, 'product_select');
		if (!isset($data['select']) || $data['select'] == 0) {
			$data['products'] = $this->postRepository->all()->where('type', 'product');
		} else {
			$data['products'] = $this->termRepository->getPostsByTermID($data['select']);
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
			->addColumn('delete', function ($product) {
				return view('phobrv::product.components.deleteBtn', ['product' => $product]);
			})
			->make(true);
	}

	public function create() {
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
			return view('phobrv::product.create')->with('data', $data);
		} catch (Exception $e) {
			return back()->with('alert_danger', $e->getMessage());
		}
	}

	public function store(Request $request) {
		$request->merge(['slug' => $this->vstring->standardKeyword($request->title)]);
		$request->validate([
			'slug' => 'required|unique:posts',
		]);

		$data = $request->all();
		$data['user_id'] = Auth::id();
		$data['type'] = $this->type;
		$post = $this->postRepository->create($data);

		$msg = __('Create prodcut success!');

		if ($request->typeSubmit == 'save') {
			return redirect()->route('product.index')->with('alert_success', $msg);
		} else {
			return redirect()->route('product.edit', ['product' => $post->id])->with('alert_success', $msg);
		}

	}

	public function show($id) {
		//
	}

	public function edit($id) {
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
			$data['arrayGroupID'] = $this->termRepository->getArrayTermIDByTaxonomy($data['post']->terms, $this->taxonomy);
			$data['gallery'] = $this->postRepository->getMultiMetaByKey($data['post']->postMetas, "image");
			$data['meta'] = $this->postRepository->getMeta($data['post']->postMetas);
			return view('phobrv::product.edit')->with('data', $data);
		} catch (Exception $e) {
			return back()->with('alert_danger', $e->getMessage());
		}
	}

	public function update(Request $request, $id) {
		$request->merge(['slug' => $this->vstring->standardKeyword($request->title)]);
		$request->validate([
			'slug' => 'required|unique:posts,slug,' . $id,
		]);
		$data = $request->all();

		$post = $this->postRepository->update($data, $id);

		if (isset($data['group'])) {
			$post->terms()->sync($data['group']);
		}
		$this->postRepository->handleSeoMeta($post, $request);
		$msg = __('Update  prodcut success!');
		if ($request->typeSubmit == 'save') {
			return redirect()->route('product.index')->with('alert_success', $msg);
		} else {
			return redirect()->route('product.edit', ['product' => $post->id])->with('alert_success', $msg);
		}

	}

	public function destroy($id) {
		$this->postRepository->destroy($id);
		$msg = __("Delete post success!");
		return redirect()->route('product.index')->with('alert_success', $msg);
	}

	public function updateUserSelectGroup(Request $request) {
		$user = Auth::user();
		$this->userRepository->insertMeta($user, array('product_select' => $request->select));
		return redirect()->route('product.index');
	}

	public function uploadGallery(Request $request) {
		$data = $request->all()['data'];
		$product = $this->postRepository->find($data['product_id']);

		$images = explode(",", $data['images']);
		if ($images && count($images) > 0) {
			foreach ($images as $key => $path) {
				$this->postRepository->insertMultiMeta($product, 'image', $path);
			}
		}
		return response()->json([
			'msg' => 'success',
			'message' => 'Update gallery success!',
		]);
	}

	public function apiDelete(Request $request){
		$data = $request->all();
		$this->postRepository->destroy($data['id']);
		return response()->json(['code' => '0', 'msg' => 'success']);
	}

	public function deleteMetaAPI(Request $request) {
		$meta_id = $request->meta_id;
		$this->postRepository->removeMeta($meta_id);
		return $meta_id;
	}
}
