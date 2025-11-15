import 'package:flutter/material.dart';
import 'package:pokemons/helpers/color.utils.dart';
import 'package:pokemons/models/pokemon.dart';
import 'package:pokemons/widgets/text_widget.dart';

class PokemonDataPage extends StatelessWidget {
  final Pokemon pokemon;
  const PokemonDataPage({super.key, required this.pokemon});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        appBar: AppBar(
          backgroundColor: getColorFromName(pokemon.color),
          title: Text(
            "${pokemon.name}",
            style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
          ),
        ),
        body: Padding(
          padding: const EdgeInsets.all(8),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Center(
                  child: SizedBox(
                width: 300,
                height: 300,
                child: Image.network(
                  pokemon.imageUrl.toString(),
                  fit: BoxFit.contain,
                ),
              )),
              TextWidget(
                text: "Abilities: ${pokemon.abilities}",
                size: 15,
              ),
              const SizedBox(width: 20),
              const TextWidget(
                text: "Description:",
                size: 20,
              ),
              SizedBox(
                  width: double.infinity,
                  child: TextWidget(text: "${pokemon.flavorText}", size: 20)),
              const SizedBox(width: 20),
            ],
          ),
        ));
  }
}
