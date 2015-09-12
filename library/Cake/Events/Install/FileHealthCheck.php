<?php
namespace Cake\Events;

class Install_FileHealthCheck extends \Cake\Install_FileHealthCheckBase
{
    
    public function getFileHashes()
    {
        return array(
        	'library/Cake/Events/addon-Cake_Events.xml' => 'a573f08218e3c7475d67d08ad09faef4',
        	'library/Cake/Events/ControllerHelper/Event.php' => 'da326e5ce68aac4ba6bd8a199da2c373',
        	'library/Cake/Events/DataWriter/Definition/Event.php' => '1b27803055792898c8af7f92e2dcc68b',
        	'library/Cake/Events/DataWriter/Definition/EventTime.php' => 'e63e2b59a724ad85acbae28c0ecaa90a',
        	'library/Cake/Events/DataWriter/Event.php' => 'da75021b906e32033389f8ab5088d916',
        	'library/Cake/Events/DataWriter/EventTime.php' => '371ce14d7cc4d5dce84a903ac8f26e1a',
        	'library/Cake/Events/Helper/Date.php' => '0eeddddd82414daa12d96e296464a3e9',
        	'library/Cake/Events/Install/Controller.php' => '07c01c1c17c76feebaa11da53dbe84bf',
        	'library/Cake/Events/Install/Data.php' => '95fe84abab9e05577afcd2a5255e4dc9',
        	'library/Cake/Events/Model/Event.php' => 'de0face23e32385f302917d677a6781d',
        	'library/Cake/Events/Trait/RoutePrefix.php' => 'e4b17f9000dce199057114d20c4172fd',
        	'library/Cake/Events/ViewPublic/Add.php' => '024a30ec9fd8946a542057f05d58485d',
        	'js/cake/events/edit.js' => 'c7869c244b4128e93beb3676e8a338c0',
        	'js/cake/events/full/edit.js' => '10e442c0ce98b27585c7b2675f4bf565',
        	'js/cake/events/full/index.html' => 'd41d8cd98f00b204e9800998ecf8427e',
        	'js/cake/events/index.html' => 'd41d8cd98f00b204e9800998ecf8427e',
        );
    }
}