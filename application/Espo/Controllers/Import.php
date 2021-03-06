<?php
/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2015 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: http://www.espocrm.com
 *
 * EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EspoCRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EspoCRM. If not, see http://www.gnu.org/licenses/.
 ************************************************************************/

namespace Espo\Controllers;

use Espo\Core\Utils as Utils;
use \Espo\Core\Exceptions\Error;
use \Espo\Core\Exceptions\Forbidden;
use \Espo\Core\Exceptions\BadRequest;

class Import extends \Espo\Core\Controllers\Record
{
    protected function checkControllerAccess()
    {
        if (!$this->getUser()->isAdmin()) {
            throw new Forbidden();
        }
    }

    public function actionPatch($params, $data)
    {
        throw new BadRequest();
    }

    public function actionUpdate($params, $data)
    {
        throw new BadRequest();
    }

    public function actionMassUpdate($params, $data, $request)
    {
        throw new BadRequest();
    }

    public function actionCreateLink($params, $data)
    {
        throw new BadRequest();
    }

    public function actionRemoveLink($params, $data)
    {
        throw new BadRequest();
    }

    protected function getFileManager()
    {
        return $this->getContainer()->get('fileManager');
    }

    protected function getEntityManager()
    {
        return $this->getContainer()->get('entityManager');
    }

    public function actionUploadFile($params, $data)
    {
        $contents = $data;

        $attachment = $this->getEntityManager()->getEntity('Attachment');
        $attachment->set('type', 'text/csv');
        $attachment->set('role', 'Import File');
        $attachment->set('name', 'import-file.csv');
        $this->getEntityManager()->saveEntity($attachment);

        $this->getFileManager()->putContents('data/upload/' . $attachment->id, $contents);

        return array(
            'attachmentId' => $attachment->id
        );
    }

    public function actionRevert($params, $data)
    {
        if (empty($data['id'])) {
            throw new BadRequest();
        }
        return $this->getService('Import')->revert($data['id']);
    }

    public function actionRemoveDuplicates($params, $data)
    {
        if (empty($data['id'])) {
            throw new BadRequest();
        }
        return $this->getService('Import')->removeDuplicates($data['id']);
    }

    public function actionCreate($params, $data)
    {
        $importParams = array(
            'headerRow' => $data['headerRow'],
            'fieldDelimiter' => $data['fieldDelimiter'],
            'textQualifier' => $data['textQualifier'],
            'dateFormat' => $data['dateFormat'],
            'timeFormat' => $data['timeFormat'],
            'personNameFormat' => $data['personNameFormat'],
            'decimalMark' => $data['decimalMark'],
            'currency' => $data['currency'],
            'defaultValues' => $data['defaultValues'],
            'action' => $data['action'],
        );

        $attachmentId = $data['attachmentId'];

        if (!$this->getAcl()->check($data['entityType'], 'edit')) {
            throw new Forbidden();
        }

        return $this->getService('Import')->import($data['entityType'], $data['fields'], $attachmentId, $importParams);
    }
}

