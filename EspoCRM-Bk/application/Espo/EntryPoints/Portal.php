<?php
/************************************************************************
 * This file is part of Simply I Do.
 *
 * Simply I Do - Open Source CRM application.
 * Copyright (C) 2014-2017 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: http://www.espocrm.com
 *
 * Simply I Do is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Simply I Do is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Simply I Do. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Simply I Do" word.
 ************************************************************************/

namespace Espo\EntryPoints;

use \Espo\Core\Exceptions\NotFound;
use \Espo\Core\Exceptions\Forbidden;
use \Espo\Core\Exceptions\BadRequest;

class Portal extends \Espo\Core\EntryPoints\Base
{
    public static $authRequired = false;

    public function run($data = array())
    {
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];
        } else if (!empty($data['id'])) {
            $id = $data['id'];
        } else {
            $url = !empty($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : $_SERVER['REQUEST_URI'];

            $id = explode('/', $url)[count(explode('/', $_SERVER['SCRIPT_NAME'])) - 1];
            if (!$id) {
                $id = $this->getConfig()->get('defaultPortalId');
            }
            if (!$id) {
                throw new NotFound();
            }
        }

        $application = new \Espo\Core\Portal\Application($id);
        $application->setBasePath($this->getContainer()->get('clientManager')->getBasePath());
        $application->runClient();
    }
}
