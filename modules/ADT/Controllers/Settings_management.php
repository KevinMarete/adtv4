<?php

namespace Modules\ADT\Controllers;

ob_start();

use App\Libraries\Ftp;
use \Modules\Tables\Controllers\Tables;
use \Modules\Template\Controllers\Template;
use App\Libraries\Mysqldump;
use App\Libraries\Encrypt;
use App\Libraries\Zip;
use \Modules\ADT\Models\User;
use \Modules\ADT\Models\Facilities;
use \Modules\ADT\Models\Access_log;
use \Modules\ADT\Models\Transaction_type;
use \Modules\ADT\Models\Drug_source;
use \Modules\ADT\Models\Drugcode;
use \Modules\ADT\Models\Drug_destination;
use \Modules\ADT\Models\CCC_store_service_point;
use \Modules\ADT\Models\Drug_Stock_Movement;
use Illuminate\Database\Capsule\Manager as DB;
use Modules\ADT\Models\Access_level;
use Modules\ADT\Models\Menu;

class Settings_management extends \App\Controllers\BaseController {

    function __construct() {

        if (!session()->get("link_id")) {
            session()->set("link_id", "index");
            session()->set("linkSub", "user_management");
        }
    }

    public function index() {
        $access_level = session()->get('user_indicator');
        if ($access_level == "system_administrator") {
            $data['settings_view'] = '\Modules\ADT\Views\\settings_system_admin_v';
        } else {
            $data['content_view'] = "\Modules\ADT\Views\\settings_v";
        }
        $this->base_params($data);
    }

    public function base_params($data) {
        $data['title'] = "System Settings";
        $data['banner_text'] = "System Settings";
        $data['link'] = "\Modules\ADT\Views\\settings_management";
        echo view("\Modules\ADT\Views\\template", $data);
    }

    public function getMenus() {
        $menus = Menu::where('active', '1')->get();
        echo json_encode($menus);
    }

    public function getAccessLevels() {
        $access = Access_level::getAllHydrated();
        echo json_encode($access);
    }

    public function getActiveAccessLevels() {
        $access = Access_level::where('active', 1)->get()->toArray();
        echo json_encode($access);
    }

}

ob_get_clean();
?>