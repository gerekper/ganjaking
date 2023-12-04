import React from 'react'
import ListIcon from './ListIcon'

const DocListItem = ({ doc, listIcon }) => {
    return (
        <li key={doc.id}>
            {listIcon?.length > 0 && <ListIcon icon={listIcon} />}
            <a href={void 0}>
                {decodeEntities(doc.title.rendered)}
            </a>
        </li>
    )
}

export default DocListItem
