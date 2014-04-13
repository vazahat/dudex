<?php

class GPHOTOVIEWER_CMP_FbAction extends OW_Component
{
    const POSITION_LEFT = 'ow_tooltip_top_left';
    const POSITION_RIGHT = 'ow_tooltip_top_right';

    private $position;

    private $actions = array();

    public function __construct( $position = self::POSITION_RIGHT )
    {
        parent::__construct();

        $this->position = $position;
    }

    public function addAction( BASE_ContextAction $action )
    {
        if ( $action->getParentKey() == null )
        {
            $this->actions[$action->getKey()]['action'] = $action;
        }
        else
        {
            if ( !empty($this->actions[$action->getParentKey()]) )
            {
                $this->actions[$action->getParentKey()]['subactions'][$action->getKey()] = $action;
            }
        }

        if ( $action->getOrder() === null )
        {
            $order = $action->getParentKey() === null
                ? count($this->actions)
                : count($this->actions[$action->getParentKey()]['subactions']);

            $action->setOrder($order);
        }
    }

    public function sortActionsCallback( $a1, $a2 )
    {
        $o1 = $a1->getOrder();
        $o2 = $a2->getOrder();

        $o1 = $o1 === null ? 0 : $o1;
        $o2 = $o2 === null ? 0 : $o2;

        if ($o1 == $o2)
        {
            return 0;
        }

        if ( $o1 === -1 )
        {
            return 1;
        }

        if ( $o2 === -1 )
        {
            return -1;
        }

        return ($o1 < $o2) ? -1 : 1;
    }

    public function sortParentActionsCallback( $a1, $a2 )
    {
        return $this->sortActionsCallback($a1['action'], $a2['action']);
    }

    public function render()
    {
        if ( !count($this->actions) )
        {
            $this->setVisible(false);
        }
        else
        {
            $visible = true;
            foreach ( $this->actions as & $action )
            {
                if ( empty($action['subactions']) && !$action['action']->getLabel() )
                {
                    $visible = false;
                    break;
                }

                if ( !empty($action['subactions']) )
                {
                    usort($action['subactions'], array($this, 'sortActionsCallback'));
                }
            }
            $this->setVisible($visible);
        }
		
        usort($this->actions, array($this, 'sortParentActionsCallback'));

        $this->assign('actions', $this->actions);

        $this->assign('position', $this->position);
		$this->setVisible(true);
        return parent::render();
    }
}

