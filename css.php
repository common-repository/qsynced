<?php
if ( ! defined('ABSPATH') ){ echo 'lero lero no puedes acceder'; die;}/*esto es por seguridad por si alguien intenta acceder remotamente*/
?>
<style>
    .qsync{
        text-align: center;
        margin: 50px;
    }
    .qsync.reduced{
        margin: 50px 20px;        
    }
    .qsync.left{
        text-align: left;
    }
    .qsync h1{
        margin-bottom: 30px;
    }
    .qsync h2, .qsync h3{
        margin-bottom: 10px;
    }
    .qsync hr{
        width: 30%;
        margin: 30px 35%;
    }
    .qsync input{
        width: 40%;
        font-size: 1.7em;
        line-height: 2em;
        text-align: center;
    }
    .qsync button{
        background-color: #236e67;
        color: white;
        width: 10%;
        font-size: 1.8em;
        padding: 0px;
        line-height: 2em;
        text-align: center;
        border-radius: 50px;
        border: none;
    }
    .qsync button:hover{
        background-color: #29b4a7;
    }
    .qsync .success{
        color: #29b4a7;
        font-weight: bold;
    }
    .qsync .error{
        color: red;
        font-weight: bold;
    }
    .qsync table.center{
        text-align: center;
    }
    .qsync table{
        width: 100%;
        border-spacing: 0px;
    }
    .qsync table td{
        font-size: 14px;
        padding: 20px 10px 20px 0;
        line-height: 1.3;
        font-weight: 600;
    }
    .qsync table textarea{
        font-size: 16px;
        text-align: justify;
        padding: 0px 8px;
        width: 60%;
    }
    .qsync table input[type=checkbox],
    .qsync table input[type=radio]{
        width: 30px !important;
        height: 30px !important;
        border-radius: 50px;
    }
    .qsync table input[type=color]{
        width: 90px;
        height: 30px;
    }
    .qsync table input[type=checkbox]:checked::before {
        height: 30px !important;
        width: 30px !important;
        margin-top: -5px !important;
    }
    .qsync table input[type=radio]:checked::before {
        content: "";
        border-radius: 50%;
        width: 30px;
        height: 30px;
        margin-top: -1px;
        margin-left: -1px;
        background-color: #29b4a7;
    }
    .qsync table th{
        text-align: center;
        background-color: #236e67;
        color: white;
        padding: 3px;
        margin: 0px;
        vertical-align: middle;
        font-size: 18px;
        font-weight: 600;
        border-left: solid 1px white;
    }
    .qsync table.center td{
        border-left: solid 1px #236e67;
        border-bottom: solid 1px #236e67;
    }
    .qsync table.center td:last-child{
        border-right: solid 1px #236e67;
    }
    .qsync table th table th{
        border-left: none;
    }
    .qsync .help{
        font-size: 14px;
        font-weight: bold;
    }
    .qsync .subth{
        font-size: 14px;
        padding-bottom: 0px;
    }
    td{
        vertical-align: middle;
        padding: 5px 3px !important;
    }
    .rowproduct button{
        margin: 1px;
        padding: 8px;
        line-height: 12px;
        font-size: 10px;
        width: auto !important;
    }
    .rowproduct input[type=number]{
        padding: 3px;
        font-size: 12px;
        width: 50% !important;        
    }
    .qsync .previewmail button{
        width: 90%;
        font-size: 1.2em !important;
        padding: 0 8px !important;
        line-height: 1.5em !important;
        font-weight: 400 !important;
    }
    .qsync .previewmailstart button{
        width: 15%;
        font-size: 1.5em !important;
        padding: 0 8px !important;
        line-height: 1.5em !important;
        font-weight: 400 !important;
    }
    .previewtemplate{
        width: 700px;
        max-height: 450px;
        overflow: auto;
        margin: 20px 0;
    }
    .previewtemplate td.adjust{
        padding: 36px 48px !important;
    }
    .previewtemplate table.adjust{
        padding: 0 50px;
    }
</style>
<?php