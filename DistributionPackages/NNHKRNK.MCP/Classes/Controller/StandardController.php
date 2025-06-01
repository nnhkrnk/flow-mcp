<?php
declare(strict_types=1);

namespace NNHKRNK\MCP\Controller;

/*
 * This file is part of the Neos.Welcome package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */


use Neos\Flow\Annotations as Flow;
use Neos\Flow\Http\Helper;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\Mvc\Exception\StopActionException;
use Neos\Flow\Package\Exception\UnknownPackageException;
use Neos\Flow\Package\PackageManager;

/**
 * Controller with a welcome start screen for Flow
 */
class StandardController extends ActionController
{
    /**
     * @var PackageManager
     * @Flow\Inject
     */
    protected $packageManager;

    /**
     * Index action
     *
     * @return void
     * @throws UnknownPackageException
     */
    public function indexAction(): void
    {
        $this->view->assign('flowPathRoot', realpath(FLOW_PATH_ROOT));
        $this->view->assign('flowPathWeb', realpath(FLOW_PATH_WEB));
        $this->view->assign('isPackageAvailable', $this->packageManager->isPackageAvailable('Happy.Coding'));

        $this->view->assign('isWindows', DIRECTORY_SEPARATOR !== '/');

        $flowPackage = $this->packageManager->getPackage('Neos.Flow');
        $version = $flowPackage->getInstalledVersion();
        $this->view->assign('version', $version);

        $this->view->assign('notDevelopmentContext', !$this->objectManager->getContext()->isDevelopment());
    }

    /**
     * @return void
     * @throws StopActionException
     */
    public function redirectAction(): void
    {
        $this->redirect('index');
    }
}
