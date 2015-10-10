<?php
namespace Cake\Events;

class Install_FileHealthCheck extends \Cake\Install_FileHealthCheckBase
{
    
    public function getFileHashes()
    {
        return array(
        	'library/Cake/Events/addon-Cake_Events.xml' => '9b6d9016a538bb4fcb34d89b01365fae',
        	'library/Cake/Events/ControllerHelper/Event.php' => 'd53daa4e50e213cd7444a3af1cca5ac5',
        	'library/Cake/Events/DataWriter/Definition/Event.php' => '1b27803055792898c8af7f92e2dcc68b',
        	'library/Cake/Events/DataWriter/Definition/EventTime.php' => 'e63e2b59a724ad85acbae28c0ecaa90a',
        	'library/Cake/Events/DataWriter/Event.php' => 'f1c85c5a00543ae1ecb61d34890fdfd6',
        	'library/Cake/Events/DataWriter/EventTime.php' => '371ce14d7cc4d5dce84a903ac8f26e1a',
        	'library/Cake/Events/Helper/Date.php' => '0eeddddd82414daa12d96e296464a3e9',
        	'library/Cake/Events/Install/Controller.php' => '07c01c1c17c76feebaa11da53dbe84bf',
        	'library/Cake/Events/Install/Data.php' => '95fe84abab9e05577afcd2a5255e4dc9',
        	'library/Cake/Events/Model/Event.php' => 'e869eb5b81fb60312bd77fcc2f012d54',
        	'library/Cake/Events/Trait/RoutePrefix.php' => 'e4b17f9000dce199057114d20c4172fd',
        	'library/Cake/Events/ViewPublic/Add.php' => '024a30ec9fd8946a542057f05d58485d',
        	'js/cake/events/edit.js' => 'c7869c244b4128e93beb3676e8a338c0',
        	'js/cake/events/full/edit.js' => '10e442c0ce98b27585c7b2675f4bf565',
        	'js/cake/events/full/index.html' => 'd41d8cd98f00b204e9800998ecf8427e',
        	'js/cake/events/index.html' => 'd41d8cd98f00b204e9800998ecf8427e',
        );
    }
}