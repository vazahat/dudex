<?php
$plugin = OW::getPluginManager()->getPlugin('yncontactimporter');
$key = strtoupper($plugin->getKey());

//Admin Routs
OW::getRouter()->addRoute(new OW_Route('yncontactimporter-admin', 'admin/friends-inviter', "{$key}_CTRL_Admin", 'index'));
OW::getRouter()->addRoute(new OW_Route('yncontactimporter-admin-providers', 'admin/friends-inviter/providers', "{$key}_CTRL_Admin", 'provider'));
OW::getRouter()->addRoute(new OW_Route('yncontactimporter-admin-ajaxEditProvider', 'admin/friends-inviter/ajax-edit-provider', "{$key}_CTRL_Admin", 'ajaxEditProvider'));

//Frontend Routs
OW::getRouter()->addRoute(new OW_Route('yncontactimporter-import', 'friends-inviter', "{$key}_CTRL_Contactimporter", 'import'));
OW::getRouter()->addRoute(new OW_Route('yncontactimporter-invite', 'friends-inviter/invite', "{$key}_CTRL_Contactimporter", 'invite'));
OW::getRouter()->addRoute(new OW_Route('yncontactimporter-pending', 'friends-inviter/pending', "{$key}_CTRL_Contactimporter", 'pending'));
OW::getRouter()->addRoute(new OW_Route('yncontactimporter-email-queue', 'friends-inviter/email-queue', "{$key}_CTRL_Contactimporter", 'emailQueue'));
OW::getRouter()->addRoute(new OW_Route('yncontactimporter-social-queue', 'friends-inviter/social-queue', "{$key}_CTRL_Contactimporter", 'socialQueue'));
OW::getRouter()->addRoute(new OW_Route('yncontactimporter-upload', 'friends-inviter/upload', "{$key}_CTRL_Contactimporter", 'upload'));
OW::getRouter()->addRoute(new OW_Route('yncontactimporter-ajax-login', 'friends-inviter/ajax-login', "{$key}_CTRL_Contactimporter", 'ajaxLogin'));
OW::getRouter()->addRoute(new OW_Route('yncontactimporter-user-join', 'friend-join', "{$key}_CTRL_Contactimporter", 'click'));

OW::getRouter()->addRoute(new OW_Route('yncontactimporter-ajax-delete', 'friends-inviter/ajax-delete', "{$key}_CTRL_Contactimporter", 'ajaxDelete'));
OW::getRouter()->addRoute(new OW_Route('yncontactimporter-ajax-resend', 'friends-inviter/ajax-resend', "{$key}_CTRL_Contactimporter", 'ajaxResend'));

function yncontactimporter_add_auth_labels( BASE_CLASS_EventCollector $event )
{
    $language = OW::getLanguage();
    $event->add(
        array(
            'yncontactimporter' => array(
                'label' => $language->text('yncontactimporter', 'auth_group_label'),
                'actions' => array(
                    'invite' => $language->text('yncontactimporter', 'auth_action_label_invite')
                )
            )
        )
    );
}
OW::getEventManager()->bind('admin.add_auth_labels', 'yncontactimporter_add_auth_labels');

function yncontactimporter_on_user_join( OW_Event $event )
{
	$params = $event->getParams();
	$userId = $params['userId'];
    YNCONTACTIMPORTER_BOL_JoinedService::getInstance()->onUserRegister($userId);
}
OW::getEventManager()->bind(OW_EventManager::ON_USER_REGISTER, 'yncontactimporter_on_user_join');