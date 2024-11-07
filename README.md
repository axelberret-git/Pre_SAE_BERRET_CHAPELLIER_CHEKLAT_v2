# Application Gestion des anomalies

## Introduction

L'objectif est de concevoir un projet permettant de visualiser les logs du serveur Apache et du serveur Mariadb de Wordpress et ce 
par l'intermédiaire d'une interface web.

On pourra filtrer ces logs par leurs sources ainsi que leurs types (selon la source) et leurs dates.
Cela induit qu'il existe des cas de figures précis à chaque source.

## Gestion des logs globale

La configuration de base prend l'ensemble des logs avec tous les types possibles.


## Gestion des logs Apache (Wordpress)

9 cas de figures possibles :
- Source : Apache (Wordpress) ----- Type : Tous les types
- Source : Apache (Wordpress) ----- Type : Type 500
- Source : Apache (Wordpress) ----- Type : Type 400
- Source : Apache (Wordpress) ----- Type : 500 - Memory Exhaustion
- Source : Apache (Wordpress) ----- Type : 500 - Undefined function
- Source : Apache (Wordpress) ----- Type : 404 - Page Not Found
- Source : Apache (Wordpress) ----- Type : 401 - Access Denied
- Source : Apache (Wordpress) ----- Type : 403 - Access Denied
- Source : Apache (Wordpress) ----- Type : 403 - File Permission Denied


## Gestion Des logs Mariadb (Wordpress)

6 cas de figures possibles :
- Source : Mariadb (Wordpress) ----- Type : Tous les types
- Source : Mariadb (Wordpress) ----- Type : 1045 - ER_ACCESS_DENIED_ERROR
- Source : Mariadb (Wordpress) ----- Type : 2002 - ER_BAD_HOST_ERROR
- Source : Mariadb (Wordpress) ----- Type : 1146 - ER_NO_SUCH_TABLE
- Source : Mariadb (Wordpress) ----- Type : 1064 - ER_PARSE_ERROR
- Source : Mariadb (Wordpress) ----- Type : 1040 - ER_TOO_MANY_CONNECTIONS


## Conclusion

Ceci est une application relativiement rudimentaire, en effet il sera possible d'agrandir le champ des possibles en ajoutant de nouvelles sources
ainsi que de nouveaux types d'erreur.

Cela demande néanmoins un certain maintien de l'application afin qu'elle puisse gérer les erreurs comme il se doit en fonction de l'infrastructure mise en place.

Il est également possible d'apporter des améliorations quant à l'interface web qui peut avoir une vue permettant de visualiser les performances de l'infrastructure
dans son ensemble en complémentarité avec la visualisation des logs par les différentes sources et types.
