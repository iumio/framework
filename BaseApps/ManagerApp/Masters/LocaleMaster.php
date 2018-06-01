<?php


/**
 *
 *  * This is an iumio Framework component
 *  *
 *  * (c) RAFINA DANY <dany.rafina@iumio.com>
 *  *
 *  * iumio Framework, an iumio component [https://iumio.com]
 *  *
 *  * To get more information about licence, please check the licence file
 *
 */

namespace ManagerApp\Masters;

use iumioFramework\Core\Base\Locale\AppLocale;
use iumioFramework\Core\Base\Locale\Locale;
use iumioFramework\Core\Exception\Server\Server500;
use iumioFramework\Core\Masters\MasterCore;
use iumioFramework\Core\Base\Renderer\Renderer;

/**
 * Class LocaleMaster
 * @package iumioFramework\Core\Manager
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class LocaleMaster extends MasterCore
{

    /**
     * Going to locale manager
     * @throws
     */
    public function localeActivity()
    {
        return ($this->render(
            "localemanager",
            array("selected" => "localemanager", "loader_msg" => "Locale Manager",
                "enabledlocale" => Locale::isEnabled())
        ));
    }
    /**
     * Change the status of locale
     * @param string $status Status ["disabled", "enabled"]
     * @return Renderer
     * @throws Server500
     */
    public function changeStatusActivity(string $status)
    {
        if ("enabled" === $status) {
            if (Locale::enableLocale()) {
                return ((new Renderer())->jsonRenderer(array("code" => 200, "msg" => "OK")));
            } else {
                return ((new Renderer())->jsonRenderer(array("code" => 500,
                    "results" => "Locale is already enabled")));
            }
        } elseif ("disabled" === $status) {
            if (Locale::disableLocale()) {
                return ((new Renderer())->jsonRenderer(array("code" => 200, "msg" => "OK")));
            } else {
                return ((new Renderer())->jsonRenderer(array("code" => 500,
                    "results" => "Locale is already disabled")));
            }
        }
        return ((new Renderer())->jsonRenderer(array("code" => 500,
            "results" => "Undefined locale status $status")));
    }


    /**
     * Change the locale app status
     * @param string $appname The appname
     * @param string $status Status ["disabled", "enabled"]
     * @throws Server500
     */
    public function changeStatusLocalAppActivity(string $appname, string $status)
    {
        if ("enabled" === $status) {
            $locale = new AppLocale($appname);
            $locale->setEnabled(true);
            $locale->apply();
            unset($locale);
            $locale = new AppLocale($appname);
            if ($locale->isEnabled()) {
                return ((new Renderer())->jsonRenderer(array("code" => 200, "msg" => "OK")));
            } else {
                return ((new Renderer())->jsonRenderer(array("code" => 500,
                    "results" => "Cannot enabled app locale for $appname")));
            }
        } elseif ("disabled" === $status) {
            $locale = new AppLocale($appname);
            $locale->setEnabled(false);
            $locale->apply();
            unset($locale);
            $locale = new AppLocale($appname);
            if (false === $locale->isEnabled()) {
                return ((new Renderer())->jsonRenderer(array("code" => 200, "msg" => "OK")));
            } else {
                return ((new Renderer())->jsonRenderer(array("code" => 500,
                    "results" => "Cannot disabled app locale for $appname")));
            }
        }
        return ((new Renderer())->jsonRenderer(array("code" => 500,
            "results" => "Undefined locale status $status")));
    }

    /**
     * Get all locale
     * @return Renderer
     * @throws Server500
     * @throws \Exception
     */
    public function getAllActivity():Renderer
    {
        $locales = [];
        $elems = Locale::getAll();
        foreach ($elems as $one => $value) {
            if (null === $value) {
                $locales[] = ["app" => $one, "enabled" => false, "values" => [],
                   "prefered" => null, "transtype" => null,
                   "route_status_change" =>
                       $this->generateRoute(
                           "iumio_manager_locale_manager_framework_change_status_app",
                           ["appname" => $one, "status" => "enabled"]
                       ),
                   "route_get_one_locale_app_save" =>
                       $this->generateRoute(
                           "iumio_manager_locale_manager_edit_one",
                           ["appname" => $one]
                       ),
                       "route_get_one_locale_app" =>
                           $this->generateRoute(
                               "iumio_manager_locale_manager_get_one",
                               ["appname" => $one]
                           )];
            } else {
                $locales[] = ["app" => $one, "enabled" => $value->isEnabled(), "values" => $value->getValues(),
                   "prefered" => $value->getPrefer(), "transtype" => $value->getTranstype(), "route_status_change" =>
                       $this->generateRoute(
                           "iumio_manager_locale_manager_framework_change_status_app",
                           ["appname" => $one, "status" => ((true === $value->isEnabled())? "disabled" : "enabled")]
                       ),
                   "route_get_one_locale_app_save" =>
                       $this->generateRoute(
                           "iumio_manager_locale_manager_edit_one",
                           ["appname" => $one]
                       ),
                   "route_get_one_locale_app" => $this->generateRoute(
                       "iumio_manager_locale_manager_get_one",
                       ["appname" => $one]
                   )];
            }
        }
        return ((new Renderer())->jsonRenderer(array("code" => 200, "results" => $locales)));
    }

    /**
     * Get one locale
     * @param string $appname App name
     * @return Renderer
     * @throws \iumioFramework\Core\Exception\Server\Server500
     */
    public function getOneLocaleActivity(string $appname):Renderer
    {

        $locale = new AppLocale($appname);
        $localeFormat =  ["app" => $appname, "enabled" => $locale->isEnabled(), "values" => $locale->getValues(),
            "prefered" => $locale->getPrefer(), "transtype" => $locale->getTranstype()];

        return ((new Renderer())->jsonRenderer(array("code" => 200, "results" => $localeFormat)));
    }


    /** edit one locale
     * @param $appname string App name
     * @return Renderer JSON render
     * @throws \Exception
     */
    public function editLocaleActivity(string $appname):Renderer
    {
        $allowed = ["json", "poedit"];
        $plocale = trim($this->get("request")->get("plocale"));
        $status = $this->get("request")->get("status");
        $trans_type = trim($this->get("request")->get("trans_type"));
        $values = $this->get("request")->get("locales");

        if ("" !== $trans_type && !in_array($trans_type, $allowed)) {
            return ((new Renderer())->jsonRenderer(array("code" => 500, "msg" =>
                "Undefined translation type [$trans_type]. Set allowed translation type"))
            );
        }

        if ("" == $trans_type) {
            $trans_type = null;
        }

        if ("" == $plocale) {
            $plocale = null;
        }

        if (null !== $plocale && is_array($values) && !in_array($plocale, array_keys($values))) {
            return ((new Renderer())->jsonRenderer(array("code" => 500, "msg" =>
                "Cannot set the default locale [$plocale] : Locale must be exist in locale keys"))
            );
        }

        if (!in_array($status, [true, false])) {
            return ((new Renderer())->jsonRenderer(array("code" => 500, "msg" =>
                "Unknow status $status. Please set true or false status"))
            );
        }

        if ("" == $values) {
            $values = [];
        }

        $locale = new AppLocale($appname);
        $locale->setEnabled($status);
        $locale->setPrefer($plocale);
        $locale->setAppname($appname);
        $locale->setTranstype($trans_type);
        $locale->setValues([$values]);
        $locale->apply();

        return ((new Renderer())->jsonRenderer(array("code" => 200, "msg" => "OK")));
    }
}
