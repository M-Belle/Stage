<?php

namespace Drupal\crij_professionnel\Controller;

use Drupal\Core\Controller\ControllerBase;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Unicode;


/**
 * Search Cities 
 */
class SearchCitiesController extends ControllerBase {
  /**
   * Returns response for the autocompletion.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request object containing the search string.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON response containing the autocomplete suggestions.
   */

  public function autocomplete(request $request) {
        $matches = array();
        $string = $request->query->get('q');
        if ($string) {
            $matches = array();
            $query = \Drupal::database()->select('communes', 'c');
            $query->addField('c', 'insee');
            $query->addField('c', 'code_postal');
            $query->addField('c', 'nom');
            $query->condition('c.nom', $string . '%', 'like');

            $result = $query->execute()->fetchAll();
            foreach ($result as $row) {
                $matches[] = ['value' => $row->nom . " (" . $row->code_postal . ")" , 'label' => $row->nom . " (" . $row->code_postal . ")"];
            }
        }
        return new JsonResponse($matches);
    }

}
