<?php
/**
 * Email:zhaojunlike@gmail.com
 * Date: 7/10/2017
 * Time: 7:11 PM
 */

namespace app\admin\controller\user;


use app\admin\controller\Auth;
use app\common\tools\Utils;

class User extends Auth
{

    public function addUser()
    {
        $data = [
            'username' => request()->request('username'),
            'nickname' => request()->request('nickname'),
            'password' => request()->request('password'),
            'level_score' => request()->request("level_score"),
            'experience' => request()->request("experience"),
        ];
        //
        $data['password'] = Utils::encodeUserPassword($data['password'], $data['username']);
        $ret = model('user')->validate("user.add")->save($data);
        if (false !== $ret) {
            $this->result([], 200, '新增成功!', "JSON");
        } else {
            $this->result([], 500, "新增失败!" . model('user')->getError(), "JSON");
        }
    }

    /**
     * 用户列表
     * Email:zhaojunlike@gmail.com
     */
    public function userList()
    {
        return $this->fetch();
    }

    public function getUserList()
    {
        $map = [];
        $userList = model('user')
            ->where($map)
            ->paginate($this->page_limit);

        $data['data_list'] = $userList;

        $this->result($data, 200, 'success', "JSON");
    }

    public function delete($id)
    {
        $map = [];
        if (is_array($id)) {
        } else {
            $map['id'] = $id;
        }
        $ret = model('user')->where($map)->delete();
        if ($ret) {
            $this->result([], 200, "删除成功", "JSON");
        } else {
            $this->result([], 500, "删除失败", "JSON");
        }
    }
}