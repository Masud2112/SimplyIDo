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

namespace Espo\Entities;

class User extends \Espo\Core\Entities\Person
{
    public function isAdmin()
    {
        return $this->get('isAdmin');
    }

    public function userTypeId()
    {
        return $this->get('userTypeId');
    }

    public function isSuperAdmin()
    {
        return $this->get('isSuperAdmin');
    }

    public function isActive()
    {
        return $this->get('isActive');
    }

    public function isPortalUser()
    {
        return $this->get('isPortalUser');
    }

    public function getTeamIdList()
    {
        if (!$this->has('teamsIds')) {
            $this->loadLinkMultipleField('teams');
        }
        return $this->get('teamsIds');
    }

    public function loadAccountField()
    {
        if ($this->get('contactId')) {
            $contact = $this->getEntityManager()->getEntity('Contact', $this->get('contactId'));
            if ($contact && $contact->get('accountId')) {
                $this->set('accountId', $contact->get('accountId'));
                $this->set('accountName', $contact->get('accountName'));
            }
        }
    }
}
