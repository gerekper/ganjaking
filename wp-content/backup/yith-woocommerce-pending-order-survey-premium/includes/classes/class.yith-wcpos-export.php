<?php
if (!defined('ABSPATH'))
    exit;

if (!class_exists('YITH_WCPOS_Export')) {

    class YITH_WCPOS_Export
    {

        /**
         * @var string Separator from columns in CSV files
         */
        public $fields_separator = ";";

        /**
         * @var string Separator for new lines in CSV files
         */
        public $newline_separator = "\r\n";


        public function __construct()
        {

            $this->columns = array(
                'survey_id' => __('Id', 'yith-woocommerce-pending-order-survey'),
                'survey_title' => __('Survey Title', 'yith-woocommerce-pending-order-survey'),
                'question' => __('Questions', 'yith-woocommece-pending-order-survey'),
                'answer' => __('Answer', 'yith-woocommece-pending-order-survey')
            );

            $this->no_answer = __('No Answers for this Survey', 'yith-woocommece-pending-order-survey');
            add_action('admin_init', array($this, 'export_data'));
        }

        public function export_data()
        {
            if (isset($_GET['_ywcpos_donwload'])) {

                $date = date("Y_m_d_H_i_s");

                $file_name = 'pending_survey_export_' . $date . '.csv';

                $args = array(
                    'post_type' => 'ywcpos_survey',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                );

                $all_pending_surveys = get_posts($args);

                if (count($all_pending_surveys) > 0) {
                    $items = $this->render_surveys($all_pending_surveys);
                    file_put_contents($file_name, $items);

                    yith_download_file($file_name);
                }
            }
        }

        /**
         * Build a CSV row for columns title
         *
         * @param $columns columns to be shown
         *
         * @return string CSV formatted row
         */
        public function get_csv_columns_title_row()
        {
            $csv_title = '';
            foreach ($this->columns as $key => $column) {
                $csv_title .= ucwords(strtolower($column)) . $this->fields_separator;
            }

            $csv_title .= $this->newline_separator;
            return $csv_title;
        }

        /**
         * Build a CSV row for a specific survey
         *
         * @param $survey
         */
        public function get_survey_csv($survey)
        {

            $csv_row = '';

            $all_survey_answers = get_post_meta($survey->ID, '_ywcpos_all_answers', true);

            if(!empty($all_survey_answers)){
            $questions = array_keys($all_survey_answers);


            foreach ($questions as $question) {

                $answers = $all_survey_answers[$question];
                $answers = empty($answers) ? array($this->no_answer) : $answers;

                foreach ($answers as $answer) {
                    foreach ($this->columns as $key => $column) {

                        switch ($key) {

                            case 'survey_id':
                                $csv_row .= $survey->ID . $this->fields_separator;
                                break;

                            case 'survey_title':
                                $csv_row .= '"'.trim($survey->post_title).'"' . $this->fields_separator;
                                break;

                            case 'question':
                                $csv_row .= '"'.trim( stripcslashes( $question) ).'"' . $this->fields_separator;
                                break;

                            case 'answer':
                                $csv_row .= '"'.trim(stripcslashes($answer)).'"' . $this->fields_separator;
                                break;
                        }
                    }
                    $csv_row .= $this->newline_separator;
                  
                }
                $csv_row .= $this->newline_separator;
            }
            }
            return $csv_row;
        }

        /**
         * @param $surveys
         * @return string
         */
        public function render_surveys($surveys)
        {

            $survey_csv = $this->get_csv_columns_title_row();
            foreach ($surveys as $survey) {

                $survey_csv .= $this->get_survey_csv($survey);
                $survey_csv .= $this->newline_separator;


            }

            return $survey_csv;
        }
    }
}

new YITH_WCPOS_Export();