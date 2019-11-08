<?php
/**
 * Copyright (c) Enalean, 2019-Present. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 *
 */

declare(strict_types=1);

namespace Tuleap\FRS\LicenseAgreement\Admin;

use HTTPRequest;
use Project;
use Tuleap\FRS\FRSPermissionManager;
use Tuleap\FRS\LicenseAgreement\NewLicenseAgreement;
use Tuleap\Layout\BaseLayout;
use Tuleap\Layout\IncludeAssets;
use Tuleap\Request\DispatchableWithProject;
use Tuleap\Request\DispatchableWithRequest;
use Tuleap\Request\GetProjectTrait;

class AddLicenseAgreementController implements DispatchableWithRequest, DispatchableWithProject
{
    use GetProjectTrait;

    /**
     * @var FRSPermissionManager
     */
    private $permission_manager;
    /**
     * @var \TemplateRendererFactory
     */
    private $renderer_factory;
    /**
     * @var \CSRFSynchronizerToken
     */
    private $csrf_token;
    /**
     * @var IncludeAssets
     */
    private $assets;

    public function __construct(
        \ProjectManager $project_manager,
        \TemplateRendererFactory $renderer_factory,
        FRSPermissionManager $permission_manager,
        \CSRFSynchronizerToken $csrf_token,
        IncludeAssets $assets
    ) {
        $this->project_manager    = $project_manager;
        $this->permission_manager = $permission_manager;
        $this->renderer_factory   = $renderer_factory;
        $this->csrf_token         = $csrf_token;
        $this->assets = $assets;
    }

    public function process(HTTPRequest $request, BaseLayout $layout, array $variables)
    {
        $project = $this->getProject($variables);

        $helper = new LicenseAgreementControllersHelper($this->permission_manager, $this->renderer_factory);
        $helper->assertCanAccess($project, $request->getCurrentUser());

        $content_renderer = $this->renderer_factory->getRenderer(__DIR__ . '/templates');

        $layout->includeFooterJavascriptFile($this->assets->getFileURL('frs-admin-license-agreement.js'));

        $helper->renderHeader($project);
        $content_renderer->renderToPage(
            'edit-license-agreement',
            new EditLicenseAgreementPresenter(
                $project,
                new NewLicenseAgreement(
                    '',
                    ''
                ),
                $this->csrf_token
            )
        );
        $layout->footer([]);
    }

    public static function getUrl(Project $project): string
    {
        return sprintf('/file/%d/admin/license-agreements/add', $project->getID());
    }
}