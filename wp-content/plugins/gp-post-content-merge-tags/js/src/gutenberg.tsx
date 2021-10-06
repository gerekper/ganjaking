import * as React from '@wordpress/element';
import {DropdownMenu, MenuGroup, MenuItem, Toolbar} from '@wordpress/components';
import {createHigherOrderComponent} from '@wordpress/compose';
import {BlockControls} from '@wordpress/block-editor';
import {addFilter} from '@wordpress/hooks';
import GFSVG from "./GFSVG";
import {useEffect, useState} from "@wordpress/element";
import getMergeTags from "./helpers/getMergeTags";

const insertMergeTag = (mergeTag, props) => {
    const range = window?.getSelection()?.getRangeAt(0);

    if (!range) {
        console.error('Unable to add merge tag. window.getSelection() may not be available.');
        return;
    }

    /**
     * If the start offset and end offset are the same, there isn't any select text to replace.
     * We're inserting/appending.
     */
    if (range.startOffset !== range.endOffset) {
        range.deleteContents();
    }

    range.insertNode(document.createTextNode(mergeTag));
};

const gppcmtToolbar = createHigherOrderComponent((BlockEdit) => {
    return (props) => {
        const [mergeTags, setMergeTags] = useState(null);

        useEffect(() => {
            if (!window.gppcmtData?.initFormId) {
                return;
            }

            (async () => {
                setMergeTags(await getMergeTags(  window.gppcmtData?.initFormId ));
            })();
        /* set deps to empty array so this AJAX request only runs on initial mount */
        }, []);

        return (
            <React.Fragment>
                <BlockEdit {...props} />
                <BlockControls>
                    <Toolbar>
                        {
                            mergeTags && (
                                <DropdownMenu icon={GFSVG} label="Select a merge tag to insert">
                                    {({onClose}) => (
                                        <>
                                            {
                                                Object.values(mergeTags).map((group: {
                                                    label: string,
                                                    tags: { tag: string, label: string }[]
                                                }) => (
                                                    <MenuGroup key={group.label} label={group.label}>
                                                        {
                                                            group.tags.map((mergeTag) => (
                                                                <MenuItem key={mergeTag.tag} icon={false} onClick={() => {
                                                                    insertMergeTag(mergeTag.tag, props);

                                                                    onClose();
                                                                }}>
                                                                    {mergeTag.label}
                                                                </MenuItem>
                                                            ))
                                                        }
                                                    </MenuGroup>
                                                ))
                                            }
                                        </>
                                    )}
                                </DropdownMenu>
                            )
                        }
                    </Toolbar>
                </BlockControls>
            </React.Fragment>
        );
    };
}, "withInspectorControl");

addFilter('editor.BlockEdit', 'gp-post-content-merge-tags/toolbar', gppcmtToolbar);