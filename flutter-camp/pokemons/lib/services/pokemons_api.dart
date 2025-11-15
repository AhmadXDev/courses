import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:pokemons/data/pokemon_data.dart';
import 'package:pokemons/models/pokemon.dart';

class PokemonsApi {
  String link = "https://pokeapi.co/api/v2/pokemon";

  getJsonApi(String link) async {
    var uri = Uri.parse(link);
    var response = await http.get(uri);
    var responseJson = json.decode(response.body);
    return responseJson;
  }

  getData() async {
    var responseJson = await getJsonApi(link);

    for (var pokemon in responseJson["results"]) {
      var detailJson = await getJsonApi(pokemon["url"]);

      // get Image
      String imageUrl = detailJson["sprites"]["front_default"];

      // get list of abilities names
      List<String> abilities = (detailJson["abilities"] as List)
          .map((ability) => ability["ability"]["name"] as String)
          .toList();

      // get species Json to get the color of the pokemon
      var speciesJson = await getJsonApi(detailJson["species"]["url"]);
      // get pokemon color
      String color = speciesJson["color"]["name"];

      // get flavor Text (description)
      String flavorText = speciesJson["flavor_text_entries"][0]["flavor_text"];

      allPokemons.add(Pokemon.fromJson(pokemon,
          color: color,
          flavorText: flavorText,
          imageUrl: imageUrl,
          abilities: abilities));
    }
  }
}
