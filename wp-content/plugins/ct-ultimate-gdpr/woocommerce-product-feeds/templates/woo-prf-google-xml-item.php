		<review>
			<review_id>{review_id}</review_id>
			<reviewer>
                <name is_anonymous="{name_is_anonymous}">{reviewer_name}</name>
{reviewer}
            </reviewer>
            <review_timestamp>{review_timestamp}</review_timestamp>
            <content>{review_content}</content>
            <review_url type="group">{product_url}</review_url>
            <ratings>
                <overall min="1" max="5">{review_rating}</overall>
            </ratings>
            <products>
                <product>
{product_ids}
                	<product_name>{product_name}</product_name>
                    <product_url>{product_url}</product_url>
                </product>
            </products>
            <collection_method>{collection_method}</collection_method>
		</review>
