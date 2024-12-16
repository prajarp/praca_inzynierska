export function calculateChartData(trailer) {
    const maxWeight = trailer.total_weight;
    const maxVolume = trailer.total_volume;

    const totalWeight = trailer.matrix.flat().reduce((sum, trailerItem) => {
      if (trailerItem?.bin) {
        return sum + trailerItem.bin.totalFittedWeight;
      }
      return sum;
    }, 0);

    const totalVolume = trailer.matrix.flat().reduce((sum, trailerItem) => {
      if(trailerItem?.bin) {
        return sum + trailerItem.bin.totalFittedVolume;
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