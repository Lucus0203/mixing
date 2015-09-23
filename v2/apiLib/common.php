<?php

require_once APP_DIR . DS . 'apiLib' . DS . 'db.php';
require_once APP_DIR . DS . 'apiLib' . DS . 'constant_config.php';
$db = db::getInstance();

/**
 *
 * 过滤参数
 * 
 * @return undefine
 * @author liting
 * @property created at 2012-10-29
 * @property updated at 2012-10-29
 * @example
 *
 */
function filter($value) {
        if (is_array($value)) {
                foreach ($value as $k => $v) {
                        if (is_array($v)) {
                                foreach ($v as $kk => $vv) {
                                        $v [$kk] = htmlspecialchars($vv);
                                }
                                $value [$k] = $v;
                        } else {
                                $value [$k] = htmlspecialchars($v);
                        }
                }
        } else {
                $value = htmlspecialchars($value);
        }
        return $value;
}

function filterSql($value) {
        if (!get_magic_quotes_gpc()) {
                $str = addslashes($value); // 进行过滤 
        }
        $str = str_replace("_", "\_", $str);
        $str = str_replace("%", "\%", $str);
        return $str;
}

//过滤非法字符
function filterIlegalWord($value) {
        $words = explode(',', ILLEGAL_WORD);
        if (is_array($value)) {
                foreach ($value as $k => $v) {
                        if (is_array($v)) {
                                foreach ($v as $kk => $vv) {
                                        foreach ($words as $w) {
                                                $vv = str_replace($w, '*', $vv);
                                        }
                                        $v [$kk] = htmlspecialchars($vv);
                                }
                                $value [$k] = $v;
                        } else {
                                foreach ($words as $w) {
                                        $v = str_replace($w, '*', $v);
                                }
                                $value [$k] = htmlspecialchars($v);
                        }
                }
        } else {
                foreach ($words as $w) {
                        $value = str_replace($w, '*', $value);
                }
                $value = htmlspecialchars($value);
        }
        return $value;
}

function checkEmail($value) {
        if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $value)) {
                return false;
        }
        return true;
}

function checkMobile($value) {
        if (!preg_match("/^1(3|4|5|7|8)\d{9}$/", $value)) {
                return false;
        }
        return true;
}

/**
 * 根据两点间的经纬度计算距离
 * 
 * @param float $lat
 *        	纬度值
 * @param float $lng
 *        	经度值
 */
function getDistance($lat1, $lng1, $lat2, $lng2) {
        $earthRadius = 6367000; // approximate radius of earth in meters

        /*
         * Convert these degrees to radians
         * to work with the formula
         */

        $lat1 = ($lat1 * pi()) / 180;
        $lng1 = ($lng1 * pi()) / 180;

        $lat2 = ($lat2 * pi()) / 180;
        $lng2 = ($lng2 * pi()) / 180;

        /*
         * Using the
         * Haversine formula
         *
         * http://en.wikipedia.org/wiki/Haversine_formula
         *
         * calculate the distance
         */

        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;
        $res = round($calculatedDistance / 1000, 2) . '';
        return $res;
}

function noNull($a) {
        return is_null($a) ? '' : $a;
}

function replaceNull($arr) {
        if (is_array($arr) && !empty($arr)) {
                foreach ($arr as $k => $a) {
                        if (is_array($a) && !empty($a)) {
                                $arr[$k] = replaceNull($a);
                        } else {
                                $arr[$k] = is_null($a) ? '' : $a;
                        }
                }
        }
        return $arr;
}

//返回带状态的json对象
function json_result($res, $errCode = "1", $errMsg = "") {
        $res = replaceNull($res);
        $jsonStr = array('err' => $errCode, 'errMsg' => $errMsg, 'result' => $res);
        return json_encode($jsonStr);
}

/**
 * 时间差计算
 *
 * @param Timestamp $time 时间差
 * @return String Time Elapsed
 * @author Shelley Shyan
 * @copyright http://phparch.cn (Professional PHP Architecture)
 */
function time2Units($time) {
        $year = floor($time / 60 / 60 / 24 / 365);
        $time -= $year * 60 * 60 * 24 * 365;
        $month = floor($time / 60 / 60 / 24 / 30);
        $time -= $month * 60 * 60 * 24 * 30;
        $week = floor($time / 60 / 60 / 24 / 7);
        $time -= $week * 60 * 60 * 24 * 7;
        $day = floor($time / 60 / 60 / 24);
        $time -= $day * 60 * 60 * 24;
        $hour = floor($time / 60 / 60);
        $time -= $hour * 60 * 60;
        $minute = floor($time / 60);
        $time -= $minute * 60;
        $second = $time;
        $elapse = '';

        $unitArr = array('年前' => 'year', '个月前' => 'month', '周前' => 'week', '天前' => 'day',
            '小时前' => 'hour', '分钟前' => 'minute', '秒前' => 'second'
        );

        foreach ($unitArr as $cn => $u) {
                if ($year > 0) {//大于一年显示年月日
                        $elapse = date('Y/m/d', time() - $time);
                        break;
                } else if ($$u > 0) {
                        $elapse = $$u . $cn;
                        break;
                }
        }

        return $elapse;
}

//根据经纬度获取地址--百度
function getAddressFromBaidu($lng, $lat) {
        $add_json = file_get_contents("http://api.map.baidu.com/geocoder/v2/?callbakc=renderReverse&location=" . $lat . "," . $lng . "&output=json&ak=" . BAIDU_AK);
        $add = json_decode($add_json);
        if ($add->status == 0) {
                return $add->result->addressComponent->city . $add->result->addressComponent->district; //当前用户位置 formatted_address 全地址
        }
}

//根据地址获取经纬度--百度
function getLngFromBaidu($address) {
        $loc_json = file_get_contents("http://api.map.baidu.com/geocoder/v2/?address=" . $address . "&output=json&ak=" . BAIDU_AK);
        $loc = json_decode($loc_json);
        if ($loc->status == 0) {
                $res['lng'] = $loc->result->location->lng;
                $res['lat'] = $loc->result->location->lat;
                return $res;
        }
}

//获取汉字首字母
function getFirstCharter($str) {
        if (empty($str)) {
                return '';
        }
        $fchar = ord($str{0});
        if ($fchar >= ord('A') && $fchar <= ord('z'))
                return strtoupper($str{0});
        $s1 = iconv('UTF-8', 'gb2312', $str);
        $s2 = iconv('gb2312', 'UTF-8', $s1);
        $s = $s2 == $str ? $s1 : $str;
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        if ($asc >= -20319 && $asc <= -20284)
                return 'A';
        if ($asc >= -20283 && $asc <= -19776)
                return 'B';
        if ($asc >= -19775 && $asc <= -19219)
                return 'C';
        if ($asc >= -19218 && $asc <= -18711)
                return 'D';
        if ($asc >= -18710 && $asc <= -18527)
                return 'E';
        if ($asc >= -18526 && $asc <= -18240)
                return 'F';
        if ($asc >= -18239 && $asc <= -17923)
                return 'G';
        if ($asc >= -17922 && $asc <= -17418)
                return 'H';
        if ($asc >= -17417 && $asc <= -16475)
                return 'J';
        if ($asc >= -16474 && $asc <= -16213)
                return 'K';
        if ($asc >= -16212 && $asc <= -15641)
                return 'L';
        if ($asc >= -15640 && $asc <= -15166)
                return 'M';
        if ($asc >= -15165 && $asc <= -14923)
                return 'N';
        if ($asc >= -14922 && $asc <= -14915)
                return 'O';
        if ($asc >= -14914 && $asc <= -14631)
                return 'P';
        if ($asc >= -14630 && $asc <= -14150)
                return 'Q';
        if ($asc >= -14149 && $asc <= -14091)
                return 'R';
        if ($asc >= -14090 && $asc <= -13319)
                return 'S';
        if ($asc >= -13318 && $asc <= -12839)
                return 'T';
        if ($asc >= -12838 && $asc <= -12557)
                return 'W';
        if ($asc >= -12556 && $asc <= -11848)
                return 'X';
        if ($asc >= -11847 && $asc <= -11056)
                return 'Y';
        if ($asc >= -11055 && $asc <= -10247)
                return 'Z';
        //return $str{0};
        return '';
}

/*
 * 计算星座的函数 string get_zodiac_sign(string month, string day) 
 * 输入：月份，日期 
 * 输出：星座名称或者错误信息 
 */

function get_zodiac_sign($month, $day) {
// 检查参数有效性 
        if ($month < 1 || $month > 12 || $day < 1 || $day > 31)
                return (false);
// 星座名称以及开始日期 
        $signs = array(
            array("20" => "宝瓶座"),
            array("19" => "双鱼座"),
            array("21" => "白羊座"),
            array("20" => "金牛座"),
            array("21" => "双子座"),
            array("22" => "巨蟹座"),
            array("23" => "狮子座"),
            array("23" => "处女座"),
            array("23" => "天秤座"),
            array("24" => "天蝎座"),
            array("22" => "射手座"),
            array("22" => "摩羯座")
        );
        list($sign_start, $sign_name) = each($signs[(int) $month - 1]);
        if ($day < $sign_start)
                list($sign_start, $sign_name) = each($signs[($month - 2 < 0) ? $month = 11 : $month -= 2]);
        return $sign_name;
}

//函数结束 
?>
