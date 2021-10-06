import {Editor, PluginManager} from "tinymce";

declare global {
    interface Window {
        jQuery: JQueryStatic
        ajaxurl: string
        gppcmtData: {
            initFormId: number
            postId: number
            baseUrl: string
            gfBaseUrl: string
            nonce: string
        }
        gf_vars: any
        form: any
        gfMergeTagsObj: any
        tinymce: Editor & {
            PluginManager: typeof PluginManager
        }
    }
}
