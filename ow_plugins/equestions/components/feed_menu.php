<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package equestions.components
 */
class EQUESTIONS_CMP_FeedMenu extends OW_Component
{
    private $order;

    public function __construct()
    {
        parent::__construct();

        $this->addComponent('menu', $this->getMenu());

        $this->order = EQUESTIONS_BOL_FeedService::getInstance()->getDefaultOrder();
    }

    public function setOrder( $order )
    {
        $this->order = $order;
    }

    public function onBeforeRender()
    {
        parent::onBeforeRender();

        $contextActionMenu = new BASE_CMP_ContextAction();

        $contextParentAction = new BASE_ContextAction();
        $contextParentAction->setKey('question_list_order');
        $contextParentAction->setLabel('<span class="ql-sort-btn">' . OW::getLanguage()->text('equestions', 'feed_order_' . $this->order) . '</span>');
        $contextActionMenu->addAction($contextParentAction);

        $contextAction = new BASE_ContextAction();
        $contextAction->setParentKey($contextParentAction->getKey());
        $contextAction->setLabel('<span class="ql-sort-order-label">' . OW::getLanguage()->text('equestions', 'feed_order_' . EQUESTIONS_CMP_Feed::ORDER_LATEST) . '</span>');
        $contextAction->setUrl('javascript://');
        $contextAction->setKey(EQUESTIONS_CMP_Feed::ORDER_LATEST);
        $contextAction->setOrder(1);
        $contextAction->addAttribute('qorder', EQUESTIONS_CMP_Feed::ORDER_LATEST);

        $class = array('ql-sort-item');
        if ( $this->order == EQUESTIONS_CMP_Feed::ORDER_LATEST )
        {
            $class[] = 'ql-sort-item-checked';
        }

        $contextAction->setClass(implode(' ', $class));

        $contextActionMenu->addAction($contextAction);


        $contextAction = new BASE_ContextAction();
        $contextAction->setParentKey($contextParentAction->getKey());
        $contextAction->setLabel('<span class="ql-sort-order-label">' . OW::getLanguage()->text('equestions', 'feed_order_' . EQUESTIONS_CMP_Feed::ORDER_POPULAR) . '</span>');
        $contextAction->setUrl('javascript://');
        $contextAction->setKey(EQUESTIONS_CMP_Feed::ORDER_POPULAR);
        $contextAction->setOrder(2);
        $contextAction->addAttribute('qorder', EQUESTIONS_CMP_Feed::ORDER_POPULAR);

        $class = array('ql-sort-item');
        if ( $this->order == EQUESTIONS_CMP_Feed::ORDER_POPULAR )
        {
            $class[] = 'ql-sort-item-checked';
        }

        $contextAction->setClass(implode(' ', $class));

        $contextActionMenu->addAction($contextAction);

        $this->addComponent('sortControl', $contextActionMenu);
    }

    public function getMenu()
    {
        $language = OW::getLanguage();

        $menu = new BASE_CMP_ContentMenu();

        $menuItem = new BASE_MenuItem();
        $menuItem->setKey('all');
        $menuItem->setPrefix('questions');
        $menuItem->setLabel( $language->text('equestions', 'list_all_tab') );
        $menuItem->setOrder(1);
        $menuItem->setUrl(OW::getRouter()->urlForRoute('equestions-all'));
        $menuItem->setIconClass('ow_ic_lens');

        $menu->addElement($menuItem);

        if ( OW::getUser()->isAuthenticated() )
        {
            if ( OW::getPluginManager()->isPluginActive('friends') )
            {
                $menuItem = new BASE_MenuItem();
                $menuItem->setKey('friends');
                $menuItem->setPrefix('questions');
                $menuItem->setLabel( $language->text('equestions', 'list_friends_tab') );
                $menuItem->setOrder(2);
                $menuItem->setUrl(OW::getRouter()->urlForRoute('equestions-friends'));
                $menuItem->setIconClass('ow_ic_user');

                $menu->addElement($menuItem);
            }

            $menuItem = new BASE_MenuItem();
            $menuItem->setKey('my');
            $menuItem->setPrefix('questions');
            $menuItem->setLabel( $language->text('equestions', 'list_my_tab') );
            $menuItem->setOrder(3);
            $menuItem->setUrl(OW::getRouter()->urlForRoute('equestions-my'));
            $menuItem->setIconClass('ow_ic_user');

            $menu->addElement($menuItem);
        }

        return $menu;
    }
}