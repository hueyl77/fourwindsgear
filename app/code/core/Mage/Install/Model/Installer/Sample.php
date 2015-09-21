<?php
/**
 * Sample DB Installer
 *
 * @category   Mage
 * @package    Mage_Install
 * @author     JoomlArt.com
 */
class Mage_Install_Model_Installer_Sample extends Mage_Install_Model_Installer_Db
{
    /**
     * Instal Sample database
     *
     * $data = array(
     *      [db_host]
     *      [db_name]
     *      [db_user]
     *      [db_pass]
     * )
     *
     * @param array $data
     */

    /**
     * Using php mysql to install sample data
     */
    public function installSampleDB($data) {

        //$tablePrefix = (string)Mage::getConfig()->getTablePrefix();
        $tablePrefix = $data['db_prefix'];
        $base_url = $data['unsecure_base_url'];
        $base_surl = $base_url;
        if (!empty($data['use_secure'])) $base_surl = $data['secure_base_url'];

        /* Run sample_data.sql if found, by pass default sample data from Magento */
        $file = Mage::getConfig()->getBaseDir().'/sql/sample_data.sql';
        if (!is_file($file)) {
            $file = Mage::getConfig()->getBaseDir().'/sql/magento_sample_data_for_1.9.1.0.sql';
        }

        if (is_file($file)) {
            //connect to DB
            $link = mysql_connect($data['db_host'], $data['db_user'], $data['db_pass']);
            if (!$link) {
                return false;
            }
            if (!mysql_select_db($data['db_name'], $link)) {
                //close DB connection
                mysql_close ($link);
                return false;
            }

            $sqls = self::parseSQL2($file);
            if ($sqls) {
                foreach ($sqls as $sql) {
                    $sql = str_replace ('#__', $tablePrefix, $sql);
                    //excute this sql
                    if ($sql && !mysql_query($sql, $link)) {
                        //close DB connection
                        mysql_close ($link);
                    }
                }
            }

            $link = mysql_connect($data['db_host'], $data['db_user'], $data['db_pass']);

            //Update base url
            $strSQL = "UPDATE `{$tablePrefix}core_config_data` SET `value`='$base_url' where `path`='web/unsecure/base_url'";
            mysql_query($strSQL, $link);

            //Update secure_base url
            $strSQL = "UPDATE `{$tablePrefix}core_config_data` SET `value`='$base_surl' where `path`='web/secure/base_url'";
            mysql_query($strSQL, $link);

            //close DB connection
            mysql_close($link);
            return true;
        }

        return false;
    }

    public static function parseSQL2($filePath) {

        $sqls = array();

        if (!isset($filePath)) return false;

        try {
            $tempLine = '';
            $lines = file($filePath);
            // Loop through each line
            foreach ($lines as $line)
            {
                // Skip it if it's a comment
                if (substr($line, 0, 2) == '--' || $line == '')
                    continue;
                // Add this line to the current segment
                $tempLine .= $line;
                // If it has a semicolon at the end, it's the end of the query
                if (substr(trim($line), -1, 1) == ';')
                {
                    // Perform the query
                    $sqls[] = $tempLine;

                    // Reset temp variable to empty
                    $tempLine = '';
                }
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $sqls;
    }
}