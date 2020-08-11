/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 480);
/******/ })
/************************************************************************/
/******/ ({

/***/ 3:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = {
    data: function data() {
        return {};
    },

    computed: {
        getProjectId: function getProjectId() {
            if (typeof this.$route.query.project_id !== 'undefined') {
                return this.$route.query.project_id;
            }

            return false;
        },
        getUserId: function getUserId() {
            if (typeof this.$route.query.user_id !== 'undefined') {
                return this.$route.query.user_id;
            }
            return false;
        },
        project_title: function project_title() {
            var _this = this;

            if (this.projects) {
                return this.projects.title;
            } else if (this.getProjectId) {
                var index = this.allprojects.findIndex(function (x) {
                    return x.id == _this.getProjectId;
                });
                if (index !== -1) {
                    return this.allprojects[index].title;
                }
            }

            return this.__('All Project', 'pm-pro');
        },
        unsername: function unsername() {
            var _this2 = this;

            if (this.assign_user) {
                return this.assign_user.display_name;
            } else if (this.getUserId) {
                var index = this.assigned_users.findIndex(function (x) {
                    return x.id == _this2.getUserId;
                });
                if (index !== -1) {
                    return this.assigned_users[index].display_name;
                }
            }
            return this.__('All Coworker', 'pm-pro');
        },
        allprojects: function allprojects() {
            return this.$root.$store.state.projects.sort(function (a, b) {
                var atitle = a.title.toLowerCase();
                var btitle = b.title.toLowerCase();

                return atitle < btitle ? -1 : atitle > btitle ? 1 : 0;
            });
        },
        assigned_users: function assigned_users() {
            return this.$store.state.reports.assigned_users.sort(function (a, b) {
                var adisplay_name = a.display_name.toLowerCase();
                var bdisplay_name = b.display_name.toLowerCase();

                return adisplay_name < bdisplay_name ? -1 : adisplay_name > bdisplay_name ? 1 : 0;
            });;
        }
    },
    methods: {
        reduceMonth: function reduceMonth() {
            return pm.Moment().subtract(1, 'months').format('YYYY-MM-01');
        },
        getResults: function getResults(args) {
            var self = this,
                pre_define = {
                conditions: {},
                callback: false
            };

            var args = jQuery.extend(true, pre_define, args);
            var conditions = this.generateConditions(args.conditions);

            var request = {
                type: 'GET',
                url: args.url + conditions,
                success: function success(res) {
                    if (typeof args.callback === 'function') {
                        args.callback.call(self, res);
                    }
                    pm.NProgress.done();
                }
            };

            this.httpRequest(request);
        },

        // OverdueTasks (args) {
        //     var self = this,
        //     pre_define = {
        //         conditions: {
        //         },
        //         callback: false,
        //     };

        //     var args = jQuery.extend(true, pre_define, args );
        //     var  conditions = this.generateConditions(args.conditions);

        //     var request = {
        //         type: 'GET',
        //         url: self.base_url + '/pm-pro/v2/overdue-tasks?'+conditions,
        //         success (res) {
        //             if ( typeof args.callback === 'function' ) {
        //                 args.callback.call ( self, res );
        //             }
        //             pm.NProgress.done();
        //         }
        //     };

        //     this.httpRequest(request);
        // },
        // completedTasks (args) {

        //     var self = this,
        //     pre_define = {
        //         conditions: {

        //         },
        //         callback: false,
        //     };
        //     var args = jQuery.extend(true, pre_define, args );
        //     var  conditions = this.generateConditions(args.conditions);

        //     var request = {
        //         type: 'GET',
        //         url: self.base_url + '/pm-pro/v2/completed-tasks?'+conditions,
        //         success (res) {
        //             if ( typeof args.callback === 'function' ) {
        //                 args.callback.call ( self, res );
        //             }
        //             pm.NProgress.done();
        //         }
        //     };
        //     this.httpRequest(request);
        // },

        // userActivities (args) {
        //     var self = this,
        //     pre_define = {
        //         conditions: {
        //         },
        //         callback: false,
        //     };
        //     var args = jQuery.extend(true, pre_define, args );
        //     var  conditions = this.generateConditions(args.conditions);

        //     var request = {
        //         type: 'GET',
        //         url: self.base_url + '/pm-pro/v2/user-activities?'+conditions,
        //         success (res) {
        //             if ( typeof args.callback === 'function' ) {
        //                 args.callback.call ( self, res );
        //             }
        //             pm.NProgress.done();
        //         }
        //     };
        //     this.httpRequest(request);
        // },

        // projectTasks (args) {
        //     var self = this,
        //     pre_define = {
        //         conditions: {
        //         },
        //         callback: false,
        //     };
        //     var args = jQuery.extend(true, pre_define, args );
        //     var  conditions = this.generateConditions(args.conditions);

        //     var request = {
        //         type: 'GET',
        //         url: self.base_url + '/pm-pro/v2/project-tasks?'+conditions,
        //         success (res) {
        //             if ( typeof args.callback === 'function' ) {
        //                 args.callback.call ( self, res );
        //             }
        //             pm.NProgress.done();
        //         }
        //     };
        //     this.httpRequest(request);
        // },

        // milestoneTasks (args) {
        //     var self = this,
        //     pre_define = {
        //         conditions: {
        //         },
        //         callback: false,
        //     };
        //     var args = jQuery.extend(true, pre_define, args );
        //     var  conditions = this.generateConditions(args.conditions);

        //     var request = {
        //         type: 'GET',
        //         url: self.base_url + '/pm-pro/v2/milestone-tasks?'+conditions,
        //         success (res) {
        //             if ( typeof args.callback === 'function' ) {
        //                 args.callback.call ( self, res );
        //             }
        //             pm.NProgress.done();
        //         }
        //     };

        //     this.httpRequest(request);
        // },
        // unassignedTasks (args) {

        //     var self = this,
        //     pre_define = {
        //         conditions: {

        //         },
        //         callback: false,
        //     };
        //     var args = jQuery.extend(true, pre_define, args );
        //     var  conditions = this.generateConditions(args.conditions);

        //     var request = {
        //         type: 'GET',
        //         url: self.base_url + '/pm-pro/v2/unassigned-tasks?'+conditions,
        //         success (res) {
        //             if ( typeof args.callback === 'function' ) {
        //                 args.callback.call ( self, res );
        //             }
        //             pm.NProgress.done();
        //         }
        //     };

        //     this.httpRequest(request);
        // },

        getAllusers: function getAllusers(args) {
            var self = this,
                pre_define = {
                callback: false
            };

            var args = jQuery.extend(true, pre_define, args);

            var request = {
                type: 'GET',
                url: self.base_url + '/pm/v2/assigned_users/',
                success: function success(res) {
                    self.$store.commit('reports/setAssigneduser', res.data);
                    if (typeof args.callback === 'function') {
                        args.callback.call(self, res);
                    }
                    pm.NProgress.done();
                }
            };
            self.httpRequest(request);
        },
        getAdvanceReport: function getAdvanceReport(args) {
            var self = this,
                pre_define = {
                data: {
                    page: 1,
                    per_page: this.getSettings('project_per_page', 10)
                },
                callbakc: false
            };
            var args = jQuery.extend(true, pre_define, args);
            var request = {
                type: 'GET',
                data: args.data,
                url: self.base_url + '/pm-pro/v2/advance-report',
                success: function success(res, status, xhr) {

                    if (typeof args.callback === 'function') {
                        args.callback(res, status, xhr);
                    }
                    pm.NProgress.done();
                }
            };
            self.httpRequest(request);
        },
        downloadCSV: function downloadCSV(args) {
            var self = this,
                pre_define = {
                data: {
                    page: -1
                },
                callbakc: false
            };
            var args = jQuery.extend(true, pre_define, args);
            var conditions = this.generateConditions(args.conditions);

            window.location.href = args.url + conditions;
        },
        downloadPDF: function downloadPDF(args) {
            var self = this,
                pre_define = {
                data: {
                    page: -1
                },
                callbakc: false
            };
            var args = jQuery.extend(true, pre_define, args);
            var conditions = this.generateConditions(args.conditions);

            window.location.href = args.url + conditions;
        }
    }
};

/***/ }),

/***/ 480:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _mixin = __webpack_require__(3);

var _mixin2 = _interopRequireDefault(_mixin);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

PmProMixin.reports = _mixin2.default;

/***/ })

/******/ });