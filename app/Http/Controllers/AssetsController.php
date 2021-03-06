<?php
	namespace App\Http\Controllers;

	use App\Http\Controllers\Controller;
	use App\Interfaces\AssetsInterface;
	use App\Interfaces\CategoriesInterface;
	use Illuminate\Support\Facades\Cache;
	use Illuminate\Http\Request;
	use Illuminate\Http\RedirectResponse;
	use App\Http\Requests\StoreAsset;
	use App\Http\Requests\UpdateAsset;

	class AssetsController extends Controller {
		public $repository;
		public $categories_repository;

		public function __construct(AssetsInterface $repository, CategoriesInterface $categories_repository) {
			$this->repository = $repository;
			$this->categories_repository = $categories_repository;
		}

		public function index() {
			$sort_col = isset($_GET['sort']) ? $_GET['sort'] : 'category';
			$order_col = isset($_GET['order']) ? $_GET['order'] : 'asc';
			$assets = $this->repository->get_assets($sort_col, $order_col);
			return view('assets.index', ['assets' => $assets]);
		}

		public function show($id) {
			$vals = $this->repository->get_asset_by_id($id);
			return view('assets.show', ['vals' => $vals]);
		}

		public function edit($id) {
			$asset = $this->repository->get_asset_by_id($id);
			$categories = $this->categories_repository->get_categories(); //need this for the drop down select list
			$origin = isset($_GET['origin']) ? $_GET['origin'] : ""; //need this for cancel button url
			return view('assets.edit', ['asset' => $asset, 'categories' => $categories, 'origin' => $origin]);
		}

		public function update(UpdateAsset $request, $id) {
			$this->repository->update_asset($request, $id);
			if($_POST['_edit_origin'] == 'asset_index') {
				return redirect('/assets');
			} elseif($_POST['_edit_origin'] == 'asset_show') {
				return redirect('/assets/' . $id);
			}
		}

		public function create() {
			$categories = $this->categories_repository->get_categories(); //need this for the drop down select list
			return view('assets.create', ['categories' => $categories]);
		}

		public function store(StoreAsset $request) {
			$this->repository->add_asset($request);
			return redirect('/assets');
		}

		public function delete($id) {
			$this->repository->delete_asset($id);
			return redirect('/assets');
		}
	}
