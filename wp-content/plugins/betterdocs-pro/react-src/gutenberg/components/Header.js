import React from 'react'
import { Counts, Icon, Title } from './partials'


const TitleCounts = ({ count, name, widgetType, attributes, showTitle, showCount }) => {
    const { layout } = attributes;
    if( widgetType == 'category-grid' && layout === 'layout-2' ) {
        return (
            <>
                {showCount && <Counts count={count} attributes={attributes} />}
                {showTitle && <Title title={name} titleTag={ attributes?.titleTag ?? 'h2' } />}
            </>
        )
    }

    if( widgetType == 'category-box' && ( layout === 'layout-2' || layout === 'layout-3' ) ) {
        return (
                <div className={'betterdocs-dynamic-wrapper betterdocs-category-title-counts'}>
                    {showTitle && <Title title={name} titleTag={ attributes?.titleTag ?? 'h2' } />}
                    {showCount && <Counts count={count} attributes={attributes} />}
                </div>
        );
    }

    return (
        <>
            {showTitle && <Title title={name} titleTag={ attributes?.titleTag ?? 'h2' } />}
            {showCount && <Counts count={count} attributes={attributes} />}
        </>
    )
}

const Header = ({ attributes, thumbnail, name, count, widgetType }) => {
    const {
        showTitle = true,
        showIcon = true,
        showCount = true,
        layout
    } = attributes;

    return (
        <div className='betterdocs-category-header'>
            <div className='betterdocs-category-header-inner'>
                { widgetType == 'category-grid' && layout !== 'layout-2' && showIcon && <Icon icon={thumbnail} /> }
                { widgetType == 'category-box' && showIcon && <Icon icon={thumbnail} /> }

                <TitleCounts
                    showTitle={showTitle}
                    showCount={showCount}
                    name={name}
                    count={count}
                    widgetType={widgetType}
                    attributes={attributes}
                />
            </div>
        </div>
    )
}

export default Header
