<?php
/**
 * Copyright (c) Enalean, 2019 - Present. All Rights Reserved.
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
 */

declare(strict_types=1);

namespace Tuleap\Taskboard\Routing;

use AgileDashboard_MilestonePresenter;
use HTTPRequest;
use Planning_MilestonePaneFactory;
use TemplateRenderer;
use Tuleap\AgileDashboard\Milestone\AllBreadCrumbsForMilestoneBuilder;
use Tuleap\AgileDashboard\Milestone\Pane\PanePresenterData;
use Tuleap\Layout\BaseLayout;
use Tuleap\Layout\CssAsset;
use Tuleap\Layout\IncludeAssets;
use Tuleap\Request\DispatchableWithBurningParrot;
use Tuleap\Request\DispatchableWithRequestNoAuthz;
use Tuleap\Request\NotFoundException;
use Tuleap\Taskboard\AgileDashboard\TaskboardPaneInfo;

class TaskboardController implements DispatchableWithRequestNoAuthz, DispatchableWithBurningParrot
{
    /**
     * @var MilestoneExtractor
     */
    private $milestone_extractor;
    /**
     * @var TemplateRenderer
     */
    private $renderer;
    /**
     * @var AllBreadCrumbsForMilestoneBuilder
     */
    private $bread_crumbs_builder;
    /**
     * @var Planning_MilestonePaneFactory
     */
    private $pane_factory;
    /**
     * @var IncludeAssets
     */
    private $agiledashboard_assets;
    /**
     * @var IncludeAssets
     */
    private $agiledashboard_theme_assets;

    public function __construct(
        MilestoneExtractor $milestone_extractor,
        TemplateRenderer $renderer,
        AllBreadCrumbsForMilestoneBuilder $bread_crumbs_builder,
        Planning_MilestonePaneFactory $pane_factory,
        IncludeAssets $agiledashboard_assets,
        IncludeAssets $agiledashboard_theme_assets
    ) {
        $this->milestone_extractor         = $milestone_extractor;
        $this->renderer                    = $renderer;
        $this->bread_crumbs_builder        = $bread_crumbs_builder;
        $this->pane_factory                = $pane_factory;
        $this->agiledashboard_assets       = $agiledashboard_assets;
        $this->agiledashboard_theme_assets = $agiledashboard_theme_assets;
    }

    public function process(HTTPRequest $request, BaseLayout $layout, array $variables): void
    {
        \Tuleap\Project\ServiceInstrumentation::increment(\taskboardPlugin::NAME);

        $milestone = $this->milestone_extractor->getMilestone($request->getCurrentUser(), $variables);

        $project = $milestone->getProject();
        $service = $project->getService('plugin_agiledashboard');
        if (! $service) {
            throw new NotFoundException(
                $GLOBALS['Language']->getText(
                    'project_service',
                    'service_not_used',
                    $GLOBALS['Language']->getText('plugin_agiledashboard', 'service_lbl_key')
                )
            );
        }

        $layout->includeFooterJavascriptFile($this->agiledashboard_assets->getFileURL('overview.js'));
        $layout->addCssAsset(new CssAsset($this->agiledashboard_theme_assets, 'scrum'));

        $service->displayHeader(
            $milestone->getArtifactTitle() . ' - ' . dgettext('tuleap-taskboard', "Taskboard"),
            $this->bread_crumbs_builder->getBreadcrumbs($request->getCurrentUser(), $project, $milestone),
            [],
            []
        );
        $this->renderer->renderToPage('taskboard', $this->getPresenter($milestone));
        $service->displayFooter();
    }

    private function getPresenter(\Planning_Milestone $milestone): AgileDashboard_MilestonePresenter
    {
        $presenter_data = $this->pane_factory->getPanePresenterData($milestone);
        $this->forceTaskboardPaneToBeTheActiveOne($presenter_data);

        return new AgileDashboard_MilestonePresenter($milestone, $presenter_data);
    }

    private function forceTaskboardPaneToBeTheActiveOne(PanePresenterData $presenter_data): void
    {
        foreach ($presenter_data->getListOfPaneInfo() as $pane_info) {
            if ($pane_info->getIdentifier() === TaskboardPaneInfo::NAME) {
                $pane_info->setActive(true);
                break;
            }
        }
    }
}