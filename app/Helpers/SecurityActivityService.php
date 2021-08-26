<?php

namespace App\Helpers;

use App\Models\SecurityActivity;
use Illuminate\Http\Request;

class SecurityActivityLogger {
    public function log(Request $request, $userId, $type)
    {
        $action = config("security.strings.{$type}", $type);
        $device = $this->getDevice($request->userAgent());
        $location = $this->getLocation($request->ip());

        SecurityActivity::create([
            'user_id' => $userId,
            'action' => $action,
            'device' => $device,
            'location' => $location,
        ]);
    }

    private function getLocation($ip)
    {
        $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
        $location = '';
        try {
            $location = $details->city;
            $location .= ',' . $details->region;
            $location .= ', ' . $details->country;
        } catch (\Throwable $e) {
            if (empty($location)) {
                $location = 'Unknown Location';
            }
        }
        return $location;
    }

    private function getDevice($user_agent)
    {
        $os = $this->getOS($user_agent);
        $browser = $this->getBrowser($user_agent);

        $device = '';
        if (($os == 'Unknown OS Platform') && ($browser == 'Unknown Browser')) {
            $device = 'Unknown Device';
        } else if ($os == 'Unknown OS Platform') {
            $device = $browser;
        } else if ($browser == 'Unknown Browser') {
            $device = $os;
        } else {
            $device = $browser . ', ' . $os;
        }

        return $device;
    }

    private function getOS($user_agent)
    { 
        $os_platform = "Unknown OS Platform";

        $os_array = array(
            '/windows nt 10/i'      =>  'Windows 10',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile'
        );

        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
            }
        }

        return $os_platform;
    }

    private function getBrowser($user_agent)
    {
        $browser = "Unknown Browser";

        $browser_array = array(
            '/msie/i'      => 'Internet Explorer',
            '/firefox/i'   => 'Firefox',
            '/safari/i'    => 'Safari',
            '/chrome/i'    => 'Chrome',
            '/edge/i'      => 'Edge',
            '/opera/i'     => 'Opera',
            '/netscape/i'  => 'Netscape',
            '/maxthon/i'   => 'Maxthon',
            '/konqueror/i' => 'Konqueror',
            '/mobile/i'    => 'Handheld Browser'
        );

        foreach ($browser_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $browser = $value;
            }
        }

        return $browser;
    }
}