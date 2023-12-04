import { useEffect } from '@wordpress/element';
import { select } from '@wordpress/data';

import { duplicateBlockIdFix } from "../util/helpers";

const useDuplicateBlockIdFix = (blockPrefix, blockId, clientId, setAttributes) => {
    useEffect(() => {
        duplicateBlockIdFix({
            BLOCK_PREFIX: blockPrefix,
            blockId,
            setAttributes,
            select,
            clientId,
        });
    }, []);
}

export default useDuplicateBlockIdFix;
