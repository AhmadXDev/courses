// ignore_for_file: avoid_print

import 'package:flutter/material.dart';
import 'package:pokemons/data/pokemon_data.dart';
import 'package:pokemons/extension/push.dart';
import 'package:pokemons/pages/pokemon_data_page.dart';
import 'package:pokemons/services/pokemons_api.dart';

class HomePage extends StatelessWidget {
  const HomePage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        appBar: AppBar(
          backgroundColor: Colors.blue,
          title: const Text("Pokemons List"),
        ),
        body: FutureBuilder(
            future: PokemonsApi().getData(),
            builder: (context, snapshot) {
              if (snapshot.connectionState == ConnectionState.waiting) {
                return const Center(child: CircularProgressIndicator());
              } else if (snapshot.connectionState == ConnectionState.done) {
                return ListView.builder(
                  itemCount: allPokemons.length,
                  itemBuilder: (context, index) => InkWell(
                    onTap: () {
                      context.push(PokemonDataPage(
                        pokemon: allPokemons[index],
                      ));
                    },
                    child: Card(
                      child: Column(
                        children: [
                          Text("${allPokemons[index].name}"),
                          const SizedBox(width: 10),
                          Image.network(allPokemons[index].imageUrl.toString()),
                          const SizedBox(width: 10),
                        ],
                      ),
                    ),
                  ),
                );
              } else {
                return const Center(child: Text("No Data"));
              }
            }));
  }
}
