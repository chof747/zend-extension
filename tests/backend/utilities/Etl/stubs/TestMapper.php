<?php
class TestMapper extends Chof_Util_Etl_Map_ByMethods
{
  protected function defineTargetStructure()
  //****************************************************************************
  {
    return array(
        'Name' => 'string',
        'Street' => 'string',
        'BirthDate' => array(
            'type' => 'date',
            'pattern' => 'mysql-date'
        ),
        'Country of Residence' => 'string',
        'Cars' => 'integer',
        'Graduation Date' => 'date'
    );
  }

  protected function simpleMappings()
  //****************************************************************************
  {
    return array(
        'Cars' => 'vehicle_number',
        'BirthDate' => 'dateOfBirth',
        'Graduation Date' => 'dateOfGraduation',
        'Street' => 'address_1'
    );
  }

  protected function mapName(array $input)
  //****************************************************************************
  {
    return $input['first_name'].' '.$input['second_name'];
  }

  protected function mapCountry_of_Residence(array $input)
  //****************************************************************************
  {
    return substr($input['country_code'], 0, 2);
  }
}
?>