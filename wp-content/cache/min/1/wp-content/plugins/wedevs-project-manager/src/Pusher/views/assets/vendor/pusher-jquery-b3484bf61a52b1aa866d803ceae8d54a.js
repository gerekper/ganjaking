(function($){class PM_Pusher{constructor(){this.pusher='';this.channel='';this.authentication().registerChannel().registerEvents()}
authentication(){if(!PM_Pusher_Vars.pusher_app_key){return this}
console.log(`${PM_Pusher_Vars.api_base_url}${PM_Pusher_Vars.api_namespace}/user/1/pusher/auth`);this.pusher=new Pusher(PM_Pusher_Vars.pusher_app_key,{cluster:PM_Pusher_Vars.pusher_cluster,authEndpoint:`${PM_Pusher_Vars.api_base_url}${PM_Pusher_Vars.api_namespace}/user/1/pusher/auth`});return this}
registerChannel(){if(!this.pusher){return this}
this.channel=this.pusher.subscribe(PM_Pusher_Vars.channel+'-'+PM_Pusher_Vars.user_id);return this}
registerEvents(){if(!this.channel){return this}
var self=this;jQuery.each(PM_Pusher_Vars.events,function(key,event){self.channel.bind(event,function(data){let title=typeof data.title=='undefined'?'':data.title;let message=typeof data.message=='undefined'?'':data.message;toastr.info(title,message,{"closeButton":!0,"debug":!1,"newestOnTop":!1,"progressBar":!1,"positionClass":"toast-top-right","preventDuplicates":!1,"onclick":null,"showDuration":"500","hideDuration":"1000","timeOut":"5000","extendedTimeOut":"1000","showEasing":"swing","hideEasing":"linear","showMethod":"fadeIn","hideMethod":"fadeOut","tapToDismiss":!1});jQuery('#toast-container').addClass('pm-pro-pusher-notification-wrap')})})}}
var PM_Pusher_Action=new PM_Pusher()})('jQuery')