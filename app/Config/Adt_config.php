<?php
namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Encryption configuration.
 *
 * These are the settings used for encryption, if you don't pass a parameter
 * array to the encrypter for creation/initialization.
 */
class Adt_config extends BaseConfig
{
	public $adt_version = '4.0';
    public $server_url = 'http://197.248.7.226/api/public/api/v1/';  // ppb API url
    public $ip = '197.248.7.226'; // PPB IP address
    public $port = '80'; // PPB PORt
    public $timeout = '10'; // PPB request timeout in seconds 

    public $dhiscode = [
        'balance'         =>  'jWmWT3Nvq1P',
        'received'        =>  'XmKrTgYAPoi',
        'dispensed_packs' =>  'yP6vevc91WZ',
        'losses'          =>  'b11dZBeBzRE',
        'adjustments'     =>  'LeyPc0LYjLg',
        'adjustments_neg' =>  'O9yaDegYywr',
        'count'           =>  'GvjV9gy3OOc',
        'expiry_quant'    =>  'r9aTy1gRXUC',
        'expiry_date'     =>  'hOMc7AVsdRk',
        'out_of_stock'    =>  'aDZLiIaG8gC',
        'resupply'        =>  'R4B7KIT1mch',
        'total'           =>  'NhSoXUMPK2K',

        'fcdrr_code' => 'oP3Z3LzFSru',
        'fmaps_code' => 'oEaimb6KECH',
        'dcdrr_code' => 'sSECDHvMtQs',
        'dmaps_code' => 'UH6Mq3aC0bN'
    ];

}
