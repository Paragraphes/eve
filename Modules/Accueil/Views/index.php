<h1>Ceci est un exemple d'accueil!</h1><br>

<b>Vous pouvez accèder à des valeurs locales avec:</b><br>
<?php echo $local?><br><br>

<b>Ou vous pouvez accèder à une valeur en base de données à l'aide du manager:</b><br>
<?php var_dump($id_simple)?><br><br>

<b>Voir lister plusieurs valeurs pour un critère donné:</b><br>
<?php var_dump($id_liste)?><br><br>

A noter: le manager va accèder la table du nom de [Module]_[Entité] dans la base de données. Cette table
doit avoir une structure qui correspond à celle de l'entité. Pour cet exemple, la table s'appelle
<b>Accueil_language</b>. <i>Attention, ce nom est sensible à la casse.</i><br><br>

Cliquez <a href="/eve/Admin/">ici</a> pour utiliser la page admin.
<br><br>

Vous pouvez créer plusieurs pages avec un seul module, en leur donnant une action différente dans la
table des routes, par exemple, <a href="/eve/Test/">cette page</a> est aussi gérée par le module Accueil,
mais ne se trouve pas à la même URL. 
Elle ne contient qu'un JSON, mais son contenu pourrait être géré à l'aide d'une vue dans le fichier
<b>/Modules/Accueil/Views/test.php</b>.