<?php
/**
 * Data Transfer Object for `usersetting` table.
 *
 * @author trunglt
 * @package ow.plugin.ynsocialpublisher.bol
 * @since 1.01
 */
class YNSOCIALPUBLISHER_BOL_Usersetting extends OW_Entity
{
    public $id, $userId, $key, $option, $providers;
}