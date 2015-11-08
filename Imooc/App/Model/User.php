<?php
/**
 * User: Run
 * Date: 下午5:17 15-4-14
 * File: User.php
 * Desc:
 */

namespace Imooc\App\Model;

use Imooc\Lib\Factory;
use Imooc\Lib\Model;

class User extends Model
{
    function getInfo($id)
    {
        return Factory::getDatabase('slave')->query('SELECT * FROM user WHERE id=' . $id)->fetch_assoc();
    }

    /**
     * @param $array
     */
    function create($uinfo)
    {
        $this->notify($uinfo);
    }
}