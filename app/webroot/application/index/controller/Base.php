<?php
/**
 * Email:zhaojunlike@gmail.com
 * Date: 7/8/2017
 * Time: 5:28 PM
 */

namespace app\index\controller;


use app\common\cache\AuthCache;
use app\common\model\BbsCategory;
use app\common\model\BbsPost;
use app\common\model\FriendLinks;
use app\common\model\User;
use function Couchbase\defaultDecoder;
use oeynet\addCaptcha\CaptchaHelper;
use think\captcha\Captcha;
use think\Config;
use think\Controller;
use think\Session;

class Base extends Controller
{
    protected $page_limit = 50;
    protected $mUser = null;
    protected $mAuthMenu = null;
    protected $friendLinks = [];
    protected $category = null;

    protected function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $total = [
            'current_viewer' => mt_rand(0, 20),
            'login_count' => 10,
            'current_online' => mt_rand(0, 10),
            'user_count' => User::where([])->count(),
            'today_post_count' => BbsPost::where("create_time", ">=", strtotime(date("Y-m-d")))->count(),
            'post_count' => BbsPost::where([])->count(),
        ];
        $this->page_limit = Config::get('WEB_INDEX_PAGE_LIMIT');
        $this->assign('_total', $total);
        $this->initFriendLinks();
        $this->initCategory();
        $this->initMenu();
        $this->checkLogin();
    }

    private function checkLogin()
    {
        $token = Session::get("user_token");
        if (!$token) {
            //$this->error("请先登陆系统后操作", url("portal/login"));
            //默认以游客身份进行访问
            $token = [
                'nickname' => '游客',
                'uid' => -1,
                'id' => -1,
                'ip' => request()->ip(),
                'admin' => [
                    'is_root' => 0,
                ],
            ];
        }
        //$this->mUser = $token;
        $this->mUser = User::get(['id' => $token['id']]);
        $this->assign('_user', $this->mUser);
    }

    protected function initMenu()
    {
        $tree = AuthCache::getAuthRulesTree(1, $this->request->module());
        $this->mAuthMenu = $tree->DeepTree();
        $this->assign('_menu', $this->mAuthMenu);
    }


    protected function initCategory()
    {
        $data = BbsCategory::where(['status' => 1])->order('sort ASC')->select();
        $this->assign('_category', $data);
        $this->assign('category', ['id' => 0]);
    }

    protected function initFriendLinks()
    {
        $data = FriendLinks::where(['status' => 1])->order('sort ASC')->select();
        $this->assign('_friend_links', $data);
    }


    /**
     * Email:zhaojunlike@gmail.com
     * @param int $type
     * @return bool
     */
    protected function checkVerify($type = 1)
    {
        switch ($type) {
            case 1:
                $captcha = new Captcha();
                $verify_code = $this->request->post("verify_code", null, "trim");
                if (!$captcha->check($verify_code, 1) && Config::get('app_debug') !== true) {
                    return false;
                }
                break;
            case 2:
                $captcha = new CaptchaHelper();
                $verify_code = $this->request->post("verify_code", null, "trim");
                if (!$captcha->check($verify_code, 1) && Config::get('app_debug') !== true) {
                    return false;
                }
                break;
                break;
            default:
                $captcha = new Captcha();
                $verify_code = $this->request->post("verify_code", null, "trim");
                if (!$captcha->check($verify_code, 1) && Config::get('app_debug') !== true) {
                    return false;
                }
                break;
                break;
        }
        return true;
    }
}