<?php
class TestMapperOnlyComplex extends Chof_Util_Etl_Map_ByMethods
{
  protected function mapName(array $input)
  //****************************************************************************
  {
    return eval('return $input["first_name"]." ".$input["second_name"];');
  }

  protected function mapCountry_of_Residence(array $input)
  //****************************************************************************
  {
    return substr($input['country_code'], 0, 2);
  }
}
?>