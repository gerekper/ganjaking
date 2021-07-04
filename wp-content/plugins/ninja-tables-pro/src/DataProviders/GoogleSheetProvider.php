<?php

namespace NinjaTablesPro\DataProviders;

class GoogleSheetProvider
{
    public function getData($tableId, $url)
    {
        $columns = array();
        foreach (ninja_table_get_table_columns($tableId) as $column) {
            $columns[$column['original_name']] = $column;
        }
        $data = $this->getDataFromUrl($url, $columns);

        if(is_wp_error($data)) {
            return [];
        }

        return array_map(function ($row) use ($columns) {
            $newRow = array();
            foreach ($columns as $key => $column) {
                $newRow[$column['key']] = $row[$key];
            }
            return $newRow;
        }, $data);
    }

    public function getColumns($url)
    {
        $url = $this->sanitizeGoogleUrl($url);
        try {
            $dom = new \DomDocument;
            libxml_use_internal_errors(true);
            $dom->preserveWhiteSpace = false;
            $dom->encoding = 'UTF-8';
            $dom->loadHTML('<?xml encoding="utf-8" ?>'.$this->getRemoteContents($url));
            libxml_clear_errors();
            $xpath = new \DomXPath($dom);

            $columns = [];
            $firstRow = $xpath->query('//table//tbody//tr')->item(0);
            if ($firstRow) {
                foreach ($firstRow->getElementsByTagName('td') as $index => $node) {
                    $headerName = trim($node->nodeValue);
                    if(!$headerName) {
                        $headerName = 'nt_header_'.$index;
                    }
                    $columns[$headerName] = $headerName;
                }
            }

            return $columns;
        } catch (\Exception $e) {
            return new \WP_Error(423, $e->getMessage());
        }
    }

	/**
	 * Get remote contents using either file_get_contents or curl.
	 * 
	 * @param string $url
	 * @return string
	 */
	private function getRemoteContents($url)
	{
		$allowUrlFopen = intval(ini_get('allow_url_fopen'));

		if ($allowUrlFopen == 1) {
			return file_get_contents($url);
		} else {
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

			$data = curl_exec($ch);
			curl_close($ch);

			return $data;
		}
    }

    private function getDataFromUrl($url, $tableColumns)
    {
        $url = $this->sanitizeGoogleUrl($url);

        $columns = [];
        try {
            $dom = new \DomDocument();
            libxml_use_internal_errors(true);
            $dom->preserveWhiteSpace = false;
            $dom->encoding = 'UTF-8';
            $dom->loadHTML('<?xml encoding="utf-8" ?>'.$this->getRemoteContents($url));
            libxml_clear_errors();
            $xpath = new \DomXPath($dom);
            $columns = [];
            $allRows = $xpath->query('//table//tbody//tr');

            $firstRow = $allRows->item(0);
            if ($firstRow) {
                foreach ($firstRow->getElementsByTagName('td') as $index => $node) {
                    $headerName = trim($node->nodeValue);
                    if(!$headerName) {
                        $headerName = 'nt_header_'.$index;
                    }

                    if(isset($tableColumns[$headerName])) {
                        $columns[$index] = $headerName;
                    } else {
                        $columns[$index] = false;
                    }
                }
            }

        } catch (\Exception $e) {
            return new \WP_Error(423, $e->getMessage());
        }

        if (!$columns) {
            return new \WP_Error(423, 'No Columns found');
        }

        $result = [];

        $validColumns = array_filter($columns);

        foreach ($allRows as $index => $row) {
            if($index == 0) continue;
            $newRow = [];

            if(!$row)  continue;
            foreach ($row->getElementsByTagName('td') as $columnIndex => $td) {
                if(empty($columns[$columnIndex])) {
                    continue;
                }

                $isHtml = $tableColumns[$columns[$columnIndex]]['data_type'] == 'html';
                if($isHtml) {
                    $innerHTML= '';
                    $children = $td->childNodes;
                    if($children) {
                        foreach ($children as $child) {
                            $innerHTML .= $child->ownerDocument->saveXML( $child );
                        }
                    }
                } else {
                    $innerHTML = $td->nodeValue;
                }
                if ($innerHTML != '0' && !$innerHTML) {
                    $innerHTML = ''; // adding empty string
                }
                $newRow[] = $innerHTML;
            }

            if(array_filter($newRow)) {
                $result[] = array_combine($validColumns, $newRow);
            }

        }

        return $result;
    }

    private function sanitizeGoogleUrl($url)
    {
        if(strpos($url, 'pubhtml')) {
            return $url;
        }

        $parsedUrl = parse_url($url);
        parse_str($parsedUrl['query'], $query);
        unset($query['output']);
        $query = build_query($query);
        $path = substr($parsedUrl['path'], 0, strrpos($parsedUrl['path'], '/'));
        $url = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $path . '/pubhtml?' . $query;
        return $url;
    }
}
