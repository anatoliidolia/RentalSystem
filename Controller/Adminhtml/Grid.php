<?php
declare(strict_types=1);

namespace PeachCode\RentalSystem\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;

abstract class Grid extends Action
{
    public const ADMIN_RESOURCE = 'PeachCode_RentalSystem::rental';

    /**
     * Init page
     *
     * @param Page $resultPage
     * @return Page
     */
    public function initPage(Page $resultPage): Page
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('PeachCode'), __('PeachCode'))
            ->addBreadcrumb(__('Rental System'), __('Rental System'));

        return $resultPage;
    }
}
