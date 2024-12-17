export function calculateChartData(trailer) {
    const maxWeight = trailer.total_weight;
    const maxVolume = trailer.total_volume;
    console.log(trailer);

    const totalWeight = trailer.matrix.flat().reduce((sum, trailerItem) => {
      if (trailerItem?.bin) {
          const itemsWeight = trailerItem.bin.fittedItems?.reduce((itemSum, item) => {
              return itemSum + (item.weight || 0); // Dodaj wagę itemów
          }, 0) || 0;
          return sum + trailerItem.bin.totalFittedWeight + itemsWeight;
      }
      return sum;
  }, 0);

    const totalVolume = trailer.matrix.flat().reduce((sum, trailerItem) => {
      if(trailerItem?.bin) {
        return sum + trailerItem.bin.volume;
      }
      return sum;
    }, 0);

    const remainingTrailerWeight = maxWeight - totalWeight;
    const remainingTrailerVolume = maxVolume - totalVolume;

    const colors = [];
    const labels = [];
    const remainingWeight = [];
    const remainingVolume = [];

    colors.push('#90EE90');
    colors.push('#ff6384');
    remainingWeight.push(remainingTrailerWeight);
    remainingVolume.push(remainingTrailerVolume);

    return {
      colors,
      labels,
      remainingTrailerWeight,
      remainingTrailerVolume,
      totalWeight,
      totalVolume,
      maxVolume,
    };
  }