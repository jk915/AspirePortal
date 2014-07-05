<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "login";
$route['404_override'] = '';
$route['admin/(.*)']					= "admin/$1";
$route['postback/(.*)']					= "postback/$1";
$route['login'] = "login";
$route['login/(.*)'] = "login/$1";
$route['dashboard'] = "dashboard/index";
$route['dashboard/(.*)'] = "dashboard/$1";
$route['partners'] = "partners";
$route['partners/(.*)'] = "partners/$1";
$route['contacts'] = "contacts";
$route['contacts/(.*)'] = "contacts/$1";
$route['investors'] = "investors";
$route['investors/(.*)'] = "investors/$1";
$route['advisors'] = "advisors";
$route['advisors/(.*)'] = "advisors/$1";
$route['account'] = "account";
$route['account/(.*)'] = "account/$1";
$route['media'] = "media";
$route['media/(.*)'] = "media/$1";
$route['stocklist'] = "stocklist";
$route['stocklist/(.*)'] = "stocklist/$1";
$route['tasks'] = "tasks";
$route['tasks/(.*)'] = "tasks/$1";
$route['summaries'] = "summaries";
$route['summaries/(.*)'] = "summaries/$1";
$route['leads'] = "leads";
$route['leads/(.*)'] = "leads/$1";
$route['notes'] = "notes";
$route['notes/(.*)'] = "notes/$1";
$route['areas'] = "areas";
$route['areas/(.*)'] = "areas/$1";
$route['states'] = "states";
$route['states/(.*)'] = "states/$1";
$route['regions'] = "regions";
$route['regions/(.*)'] = "regions/$1";
$route['australia'] = "australia";
$route['australia/(.*)'] = "australia/$1";
$route['projects'] = "projects";
$route['projects/(.*)'] = "projects/$1";
$route['favourites'] = "favourites";
$route['favourites/(.*)'] = "favourites/$1";
$route['myproperties'] = "myproperties";
$route['myproperties/(.*)'] = "myproperties/$1";
$route['brochure'] = "brochure";
$route['brochure/(.*)'] = "brochure/$1";
$route['terms'] = "terms";
$route['terms/(.*)'] = "terms/$1";
$route['construction'] = "construction";
$route['construction/(.*)'] = "construction/$1";
$route['cron'] = "cron";
$route['cron/(.*)'] = "cron/$1";
$route['postback'] = "postback";
$route['postback/(.*)'] = "postback/$1";
$route['announcements'] = "announcements";
$route['announcements/(.*)'] = "announcements/$1";


$route['(.*)'] = "page/show/$1";


/* End of file routes.php */
/* Location: ./application/config/routes.php */