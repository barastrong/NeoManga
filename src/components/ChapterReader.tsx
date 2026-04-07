import React from 'react';

interface ChapterReaderProps {
  imageUrls: string[];
  mangaTitle: string;
  chapterNumber: string;
}

const ChapterReader: React.FC<ChapterReaderProps> = ({ imageUrls, mangaTitle, chapterNumber }) => {
  return (
    <div className="space-y-1 mb-8 bg-black">
      {imageUrls.map((imageUrl, index) => (
        <div key={index} className="flex justify-center">
          <img
            src={imageUrl}
            alt={`${mangaTitle} - Chapter ${chapterNumber} - Page ${index + 1}`}
            className="max-w-full h-auto"
            loading="lazy"
          />
        </div>
      ))}
    </div>
  );
};

export default ChapterReader;