<?php
class ControllerToolSysinfo extends Controller {
	private $error = array();

    public function index() {
        $this->load->language('tool/sysinfo');

        $this->load->model('setting/store');

        $this->document->setTitle($this->language->get('ext_name'));

        $this->document->addStyle('view/sysinfo/css/uikit-echothemes.min.css?v=1.2.0', 'stylesheet');
        $this->document->addStyle('view/sysinfo/sysinfo.css?v='.$this->language->get('ext_version'), 'stylesheet');
        $this->document->addScript('view/sysinfo/js/uikit.min.js?v=2.8.0');

        $data['breadcrumbs']    = array();

        $data['breadcrumbs'][]  = array(
            'text'  => '<i class="uk-icon-home uk-icon-nano"></i>',
            'href'  => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
        );

        $data['breadcrumbs'][]  = array(
            'text'  => $this->language->get('ext_name'),
            'href'  => $this->url->link('tool/sysinfo', 'user_token=' . $this->session->data['user_token'], true),
        );

        $data['user_token']     = $this->session->data['user_token'];

        $data['url_phpinfo']    = $this->url->link('tool/sysinfo/phpinfo', 'user_token=' . $this->session->data['user_token'], true);


        $data['oc_version']     = VERSION;
        $data['stores']         = $this->model_setting_store->getTotalStores() + 1;
        $data['ssl']            = $this->config->get('config_secure');
        $data['seo']            = $this->config->get('config_seo_url');
        $data['maintenance']    = $this->config->get('config_maintenance');
        $data['compression']    = $this->config->get('config_compression');
        $data['captcha']        = ucwords(str_replace('_', ' ', $this->config->get('config_captcha')));
        $data['mail']           = ucwords($this->config->get('config_mail_engine'));
        $data['currency']       = $this->config->get('config_currency_auto');
        $data['error_display']  = $this->config->get('config_error_display');
        $data['error_log']      = $this->config->get('config_error_log');

        $data['db_server']      = $this->db->getServerInfo();
        $data['db_host']        = $this->db->getHostInfo();

		$data['php_version'] = phpversion();

		if (version_compare(phpversion(), '7.4.0', '<')) {
			$data['version'] = false;
		} else {
			$data['version'] = true;
		}

		$db = array(
			'mysqli',
			'pgsql',
			'pdo'
		);

		if (!array_filter($db, 'extension_loaded')) {
			$data['db'] = false;
		} else {
			$data['db'] = true;
		}

        $data['database_driver'] = strtoupper(DB_DRIVER);
        $data['database_name'] = DB_DATABASE;

        $data['database_type'] = sprintf($this->language->get('text_database_type'), $data['database_driver']);

        $data['admin_index_perm'] = substr(sprintf('%o', fileperms(DIR_APPLICATION . 'index.php')), -3);
        $data['admin_conf_perm']  = substr(sprintf('%o', fileperms(DIR_APPLICATION . 'config.php')), -3);

        $data['catalog_index_perm'] = substr(sprintf('%o', fileperms(DIR_APPLICATION . '../index.php')), -3);
        $data['catalog_conf_perm']  = substr(sprintf('%o', fileperms(DIR_APPLICATION . '../config.php')), -3);

        $data['framework_perm'] = substr(sprintf('%o', fileperms(DIR_APPLICATION . '../system/framework.php')), -3);
        $data['startup_perm']  = substr(sprintf('%o', fileperms(DIR_APPLICATION . '../system/startup.php')), -3);

        $data['operating_sys']  = php_uname();
        $data['web_server']     = $_SERVER['SERVER_SOFTWARE'];
        $data['web_host']       = $_SERVER['HTTP_HOST'];
        $data['root_path']      = $_SERVER['DOCUMENT_ROOT'];

        $data['php_i_timezone'] = ini_get('date.timezone');
        $data['date_timzone']   = ini_get('date.timezone');
        $data['php_timezone']   = date_default_timezone_get();

        $data['register_globals']   = ini_get('register_globals');
        $data['file_uploads']   = ini_get('file_uploads');
        $data['session_auto_start'] = ini_get('session_auto_start');
        $data['allow_url_fopen'] = ini_get('allow_url_fopen');
        $data['session_cookies'] = ini_get('session.use_cookies');
        $data['magic_quotes_gpc'] = ini_get('magic_quotes_gpc');

		$data['gd'] = extension_loaded('gd');
		$data['curl'] = extension_loaded('curl');
		$data['openssl'] = function_exists('openssl_encrypt');
		$data['zlib'] = extension_loaded('zlib');
		$data['zip'] = extension_loaded('zip');
		$data['xml'] = extension_loaded('xml');
		$data['iconv'] = function_exists('iconv');
		$data['mbstring'] = extension_loaded('mbstring');
		$data['sockets'] = function_exists('fsockopen');

        $data['writ_cat_index'] = is_writable(DIR_APPLICATION . '../index.php');
        $data['writ_cat_config'] = is_writable(DIR_APPLICATION . '../config.php');
        $data['writ_adm_index'] = is_writable(DIR_APPLICATION . 'index.php');
        $data['writ_adm_config'] = is_writable(DIR_APPLICATION . 'config.php');
        $data['writ_framework'] = is_writable(DIR_APPLICATION . '../system/framework.php');
        $data['writ_startup'] = is_writable(DIR_APPLICATION . '../system/startup.php');

        $data['dir_image'] = str_replace($_SERVER['DOCUMENT_ROOT'],'',(str_replace('\\','/',DIR_IMAGE)));
        $data['dir_image_perm'] = substr(sprintf('%o', fileperms(DIR_IMAGE)), -3);
        $data['dir_image_writ'] = is_writable(DIR_IMAGE);

        $data['image_cache'] = str_replace($_SERVER['DOCUMENT_ROOT'],'',(str_replace('\\','/',(DIR_IMAGE . 'cache').'/')));
        $data['image_cache_perm'] = substr(sprintf('%o', fileperms(DIR_IMAGE . 'cache')), -3);
        $data['image_cache_writ'] = is_writable(DIR_IMAGE . 'cache');

        $data['image_catalog'] = str_replace($_SERVER['DOCUMENT_ROOT'],'',(str_replace('\\','/',(DIR_IMAGE . 'catalog').'/')));
        $data['image_catalog_perm'] = substr(sprintf('%o', fileperms(DIR_IMAGE . 'catalog')), -3);
        $data['image_catalog_writ'] = is_writable(DIR_IMAGE . 'catalog');

        $data['storage_cache'] = str_replace($_SERVER['DOCUMENT_ROOT'],'',(str_replace('\\','/',DIR_CACHE)));
        $data['storage_cache_perm'] = substr(sprintf('%o', fileperms(DIR_CACHE)), -3);
        $data['storage_cache_writ'] = is_writable(DIR_CACHE);

        $data['storage_logs'] = str_replace($_SERVER['DOCUMENT_ROOT'],'',(str_replace('\\','/',DIR_LOGS)));
        $data['storage_logs_perm'] = substr(sprintf('%o', fileperms(DIR_LOGS)), -3);
        $data['storage_logs_writ'] = is_writable(DIR_LOGS);

        $data['storage_upload'] = str_replace($_SERVER['DOCUMENT_ROOT'],'',(str_replace('\\','/',DIR_UPLOAD)));
        $data['storage_upload_perm'] = substr(sprintf('%o', fileperms(DIR_UPLOAD)), -3);
        $data['storage_upload_writ'] = is_writable(DIR_UPLOAD);

        $data['storage_download'] = str_replace($_SERVER['DOCUMENT_ROOT'],'',(str_replace('\\','/',DIR_DOWNLOAD)));
        $data['storage_download_perm'] = substr(sprintf('%o', fileperms(DIR_DOWNLOAD)), -3);
        $data['storage_download_writ'] = is_writable(DIR_DOWNLOAD);

        $data['storage_modification'] = str_replace($_SERVER['DOCUMENT_ROOT'],'',(str_replace('\\','/',DIR_MODIFICATION)));
        $data['storage_modification_perm'] = substr(sprintf('%o', fileperms(DIR_MODIFICATION)), -3);
        $data['storage_modification_writ'] = is_writable(DIR_MODIFICATION);

        if (is_dir(dirname(DIR_APPLICATION) . '/vqmod')) {
			$data['detect_vqmod'] = true;
		} else {
			$data['detect_vqmod'] = false;
		}

        if (is_dir(dirname(DIR_APPLICATION) . '/vqmod/vqcache')) {
			$data['vqmod_cache'] = true;
		} else {
			$data['vqmod_cache'] = false;
		}

        if (is_dir(dirname(DIR_APPLICATION) . '/vqmod/logs')) {
			$data['vqmod_logs'] = true;
		} else {
			$data['vqmod_logs'] = false;
		}

        $data['vqmod_cache_perm'] = substr(sprintf('%o', fileperms('../vqmod/vqcache')), -3);
        $data['vqmod_cache_writ'] = is_writable('../vqmod/vqcache');

        $data['vqmod_logs_perm'] = substr(sprintf('%o', fileperms('../vqmod/logs')), -3);
        $data['vqmod_logs_writ'] = is_writable('../vqmod/logs');

        $query = $this->db->query('SELECT @@session.time_zone as timezone, now() as `datetime`;');

        $data['db_timezone']    = $query->row['timezone'];
        $data['db_datetime']    = $query->row['datetime'];
        
        $data['date_php_now']   = date('l, F d, Y');
        $data['time_php_now']   = date('h:i:s A');
        $data['date_db_now']    = date('l, F d, Y', strtotime($data['db_datetime']));
        $data['time_db_now']    = date('h:i:s A', strtotime($data['db_datetime']));
        $data['match_date']     = $data['date_php_now'] == $data['date_db_now'] ? true : false;
        $data['match_time']     = date('H:i') == date('H:i', strtotime($data['db_datetime'])) ? true : false;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('tool/sysinfo', $data));
    }

    public function phpinfo() {
        phpinfo();
   }
}