/************************************************************************
 * This file is part of Simply I Do.
 *
 * Simply I Do - Open Source CRM application.
 * Copyright (C) 2014-2017 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: http://simplyido.com
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
Espo.define('views/admin/index', 'view', function (Dep) {
   
    return Dep.extend({

        template: 'admin/index',

        data: function () {
            //console.log(this.userType);return false;
            return {
                links: this.links,
                userType: this.userType
            };
            // if(this.isAccountOwner == true){ 
            //     return {
            //         links: this.links,
            //         isAccountOwner: this.isAccountOwner
            //     };
            // }else if(this.isAdmin == true){
            //     return {
            //         links: this.links,
            //         isAdmin: this.isAdmin
            //     };
            // }else if(this.isTeamMember == true){
            //     return {
            //         links: this.links,
            //         isTeamMember: this.isTeamMember
            //     };
            // }
            
        },

        setup: function () {
            this.links = this.getMetadata().get('app.adminPanel');
            //console.log(this.getUser());return false;
            this.userType = "";
            var isSuperAdmin = this.getUser().attributes.isSuperAdmin;
            if(isSuperAdmin == 1){
                 this.userType = "admin";
            }else{
                var isSidoAdmin = this.getUser().attributes.isSidoAdmin;
                var userTypeId = this.getUser().attributes.userTypeId;
                
                if(isSidoAdmin == 1){
                    this.userType = "sidoadmin";
                }else if(userTypeId == 1){
                    this.userType = "accountowner";
                }else if(userTypeId == 2){
                    this.userType = "teammember";
                }
            }
            
           // this.iframeUrl = this.getConfig().get('adminPanelIframeUrl') || 'https://s.espocrm.com/';
        },

        updatePageTitle: function () {
            this.setPageTitle(this.getLanguage().translate('Administration'));
        },

    });
});
