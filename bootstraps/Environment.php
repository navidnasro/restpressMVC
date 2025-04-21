<?php

namespace sellerhub\bootstraps;

defined('ABSPATH') || exit;

enum Environment
{
    const PublicFolder = PluginRoot.'/public';
    const Storage  = PluginRoot.'/storage';
    const RemoteUrl = 'http://sellerclub.nahamta.com/api/';
    const IMG = ROOT.'/public/assets/img'; // path to images folder
    const CSS = ROOT.'/public/assets/css'; // path to css folder
    const JS  = ROOT.'/public/assets/js'; // path to js folder
    const TextDomain = 'restpressmvc'; // text domain name

    // database credentials
    const DbName = DB_NAME;
    const DbUser = DB_USER;
    const DbPassword = DB_PASSWORD;
    const DbHost = DB_HOST;
    const DbCharset = DB_CHARSET;
    const TablePreFix = 'restpressmvc_';
}
