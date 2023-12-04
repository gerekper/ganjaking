import { useEffect } from '@wordpress/element';
import { select } from '@wordpress/data';

const usePreviewDeviceType = (setAttributes, attributeName = 'resOption', dependencies = []) => {
    useEffect(() => {
        const editorType = window?.betterDocsProBlocksHelper?.editorType ?? 'core/edit-post';
        setAttributes({
            [attributeName]: select( editorType ).__experimentalGetPreviewDeviceType(),
        });
    }, dependencies);
}

export default usePreviewDeviceType
