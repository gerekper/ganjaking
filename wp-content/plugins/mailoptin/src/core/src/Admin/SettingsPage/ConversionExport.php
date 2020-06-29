<?php

namespace MailOptin\Core\Admin\SettingsPage;


use League\Csv\Writer;
use MailOptin\Core\Repositories\OptinCampaignsRepository;
use MailOptin\Core\Repositories\OptinConversionsRepository;

class ConversionExport
{

    /**
     * Call to export all leads.
     */
    public function all()
    {
        $this->do_export(
            OptinConversionsRepository::get_conversions()
        );
    }

    public function selected_ids($conversions_ids)
    {
        $this->do_export(
            OptinConversionsRepository::get_conversions_by_ids($conversions_ids)
        );
    }


    /**
     * Perform the actual csv export.
     *
     * @param $contents
     */
    public function do_export($data)
    {
        // ensure no output buffer gets into the csv.
        // league/csv apparently uses output buffering to save content of csv.
        ob_clean();

        $header = apply_filters('mailoptin_conversion_csv_headers', array(
            __('Campaign', 'mailoptin'),
            __('Subscriber Name', 'mailoptin'),
            __('Subscriber Email', 'mailoptin'),
            __('Custom Fields', 'mailoptin'),
            __('User Agent', 'mailoptin'),
            __('Conversion Page', 'mailoptin'),
            __('Referrer', 'mailoptin'),
            __('Date & Time', 'mailoptin')
        ));


        /** @var array $contents store array of csv content. */
        $contents = array();

        foreach ($data as $conversion) {
            // collect only the columns values excluding their column names.
            $conversion = array_values($conversion);
            // remove the conversion ID column (the first array value)
            unset($conversion[0]);
            // replace the second column (third before Id column removal above) i.e optin_id with optin campain name
            $conversion[1] = OptinCampaignsRepository::get_optin_campaign_name($conversion[1]);
            // remove the optin type column
            unset($conversion[2]);

            $contents[] = $conversion;
        }

        $writer = Writer::createFromFileObject(new \SplTempFileObject()); //the CSV file will be created using a temporary File
        $writer->setNewline("\r\n"); //use windows line endings for compatibility with some csv libraries
        $writer->setOutputBOM(Writer::BOM_UTF8); //adding the BOM sequence on output
        $writer->insertOne($header);
        $writer->insertAll($contents);
        // Because you are providing the filename you don't have to
        // set the HTTP headers Writer::output can
        // directly set them for you
        // The file is downloadable
        $writer->output('optin-conversions-' . date('m-d-Y') . '.csv');
        die;
    }


    /**
     * @return ConversionExport|null
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}