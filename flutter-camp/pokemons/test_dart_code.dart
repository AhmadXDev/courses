import 'dart:convert';
import 'package:http/http.dart' as http;

void main(List<String> args) {
  PokemonApi p = PokemonApi();
  p.getData();
}

class PokemonApi {
  String link = "https://pokeapi.co/api/v2/pokemon";

  getData() async {
    var uri = Uri.parse(link);
    var response = await http.get(uri);

    var responseJson = json.decode(response.body);
    print(responseJson["results"]);
  }
}
